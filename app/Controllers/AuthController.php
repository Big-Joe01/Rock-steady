<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Services\MailService;
use App\Services\Logger;

class AuthController extends Controller
{
    public function showLogin(): \App\Views\Response
    {
        if (auth_check()) {
            return $this->redirect('/');
        }
        
        $data = [
            'title' => 'Login | ' . APP_NAME,
        ];
        
        return $this->view('frontend/auth/login', $data);
    }

    public function login(): \App\Views\Response
    {
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['email', 'password']);
        
        if (!empty($errors)) {
            $this->withInput($_POST);
            $this->withError('Please fill in all required fields');
            return $this->back();
        }
        
        $user = User::verifyPassword($data['email'], $data['password']);
        
        if (!$user) {
            $this->withInput($_POST);
            $this->withError('Invalid email or password');
            return $this->back();
        }
        
        if ($user['status'] !== 'active') {
            $this->withError('Your account has been deactivated');
            return $this->back();
        }
        
        $_SESSION['user'] = $user;
        
        if (!empty($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            return $this->redirect($redirect);
        }
        
        return $this->redirect('/');
    }

    public function showRegister(): \App\Views\Response
    {
        if (auth_check()) {
            return $this->redirect('/');
        }
        
        $data = [
            'title' => 'Register | ' . APP_NAME,
        ];
        
        return $this->view('frontend/auth/register', $data);
    }

    public function register(): \App\Views\Response
    {
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['first_name', 'last_name', 'email', 'password', 'password_confirmation']);
        
        if (!empty($errors)) {
            $this->withInput($_POST);
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        if (!$this->validateEmail($data['email'])) {
            $this->withInput($_POST);
            $this->withError('Please enter a valid email address');
            return $this->back();
        }
        
        if (strlen($data['password']) < 8) {
            $this->withInput($_POST);
            $this->withError('Password must be at least 8 characters');
            return $this->back();
        }
        
        if ($data['password'] !== $data['password_confirmation']) {
            $this->withInput($_POST);
            $this->withError('Passwords do not match');
            return $this->back();
        }
        
        $existing = User::getByEmail($data['email']);
        if ($existing) {
            $this->withInput($_POST);
            $this->withError('An account with this email already exists');
            return $this->back();
        }
        
        $userId = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'customer',
        ]);
        
        $user = User::getById($userId);
        $_SESSION['user'] = $user;
        
        MailService::sendTemplate(
            $user['email'],
            'Welcome to ' . APP_NAME . '!',
            'welcome',
            ['user' => $user]
        );
        
        $this->withSuccess('Welcome! Your account has been created successfully.');
        
        return $this->redirect('/');
    }

    public function logout(): \App\Views\Response
    {
        unset($_SESSION['user']);
        
        return $this->redirect('/');
    }

    public function showForgotPassword(): \App\Views\Response
    {
        $data = [
            'title' => 'Forgot Password | ' . APP_NAME,
        ];
        
        return $this->view('frontend/auth/forgot-password', $data);
    }

    public function forgotPassword(): \App\Views\Response
    {
        $email = $_POST['email'] ?? '';
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->withError('Please enter a valid email address');
            return $this->back();
        }
        
        $user = User::getByEmail($email);
        
        if ($user) {
            $token = User::generateRememberToken($user['id']);
            
            MailService::sendTemplate(
                $user['email'],
                'Reset Your Password',
                'reset_password',
                [
                    'user' => $user,
                    'token' => $token,
                ]
            );
        }
        
        $this->withSuccess('If an account exists with that email, you will receive password reset instructions.');
        
        return $this->redirect('/login');
    }

    public function showResetPassword(string $token): \App\Views\Response
    {
        $data = [
            'title' => 'Reset Password | ' . APP_NAME,
            'token' => $token,
        ];
        
        return $this->view('frontend/auth/reset-password', $data);
    }
}
