<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Review;
use App\Models\Product;

class ReviewController extends Controller
{
    public function store(): \App\Views\Response
    {
        if (!auth_check()) {
            if ($this->isAjax()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Please login to write a review',
                    'requires_login' => true,
                ]);
            }
            return $this->redirect('/login');
        }
        
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['product_id', 'rating']);
        
        if (!empty($errors)) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => implode(', ', $errors)]);
            }
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        $productId = (int)$data['product_id'];
        $rating = (int)$data['rating'];
        
        if ($rating < 1 || $rating > 5) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid rating']);
            }
            $this->withError('Invalid rating');
            return $this->back();
        }
        
        $product = Product::getById($productId);
        if (!$product) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Product not found']);
            }
            return $this->back();
        }
        
        if (Review::hasUserReviewed(auth()['id'], $productId)) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'You have already reviewed this product']);
            }
            $this->withError('You have already reviewed this product');
            return $this->back();
        }
        
        Review::create([
            'product_id' => $productId,
            'user_id' => auth()['id'],
            'rating' => $rating,
            'title' => $data['title'] ?? null,
            'comment' => $data['comment'] ?? null,
            'status' => 'pending',
        ]);
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => 'Thank you! Your review has been submitted for approval.',
            ]);
        }
        
        $this->withSuccess('Thank you! Your review has been submitted for approval.');
        
        return $this->redirect('/product/' . $product['slug'] . '#reviews');
    }

    public function helpful(): \App\Views\Response
    {
        $reviewId = (int)($_POST['review_id'] ?? 0);
        
        if ($reviewId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid review']);
            }
            return $this->back();
        }
        
        Review::incrementHelpful($reviewId);
        
        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }
        
        return $this->back();
    }
}
