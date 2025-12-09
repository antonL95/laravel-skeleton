<?php

declare(strict_types=1);

return [
    'auth' => [
        'login' => [
            'title' => 'Log in to your account',
            'description' => 'Enter your email and password below to log in',
            'page_title' => 'Log in',
            'email_label' => 'Email address',
            'email_placeholder' => 'email@example.com',
            'password_label' => 'Password',
            'password_placeholder' => 'Password',
            'forgot_password' => 'Forgot password?',
            'remember_me' => 'Remember me',
            'submit' => 'Log in',
            'no_account' => 'Don\'t have an account?',
            'sign_up' => 'Sign up',
        ],

        'register' => [
            'title' => 'Create an account',
            'description' => 'Enter your details below to create your account',
            'page_title' => 'Register',
            'name_label' => 'Name',
            'name_placeholder' => 'Full name',
            'email_label' => 'Email address',
            'email_placeholder' => 'email@example.com',
            'password_label' => 'Password',
            'password_placeholder' => 'Password',
            'password_confirmation_label' => 'Confirm password',
            'password_confirmation_placeholder' => 'Confirm password',
            'submit' => 'Create account',
            'already_have_account' => 'Already have an account?',
            'log_in' => 'Log in',
        ],

        'forgot_password' => [
            'title' => 'Forgot password',
            'description' => 'Enter your email to receive a password reset link',
            'page_title' => 'Forgot password',
            'email_label' => 'Email address',
            'email_placeholder' => 'email@example.com',
            'submit' => 'Email password reset link',
            'return_to' => 'Or, return to',
            'log_in' => 'log in',
        ],

        'reset_password' => [
            'title' => 'Reset password',
            'description' => 'Please enter your new password below',
            'page_title' => 'Reset password',
            'email_label' => 'Email',
            'password_label' => 'Password',
            'password_placeholder' => 'Password',
            'password_confirmation_label' => 'Confirm password',
            'password_confirmation_placeholder' => 'Confirm password',
            'submit' => 'Reset password',
        ],

        'confirm_password' => [
            'title' => 'Confirm your password',
            'description' => 'This is a secure area of the application. Please confirm your password before continuing.',
            'page_title' => 'Confirm password',
            'password_label' => 'Password',
            'password_placeholder' => 'Password',
            'submit' => 'Confirm password',
        ],

        'two_factor' => [
            'authentication_code' => [
                'title' => 'Authentication Code',
                'description' => 'Enter the authentication code provided by your authenticator application.',
                'toggle_text' => 'login using a recovery code',
            ],
            'recovery_code' => [
                'title' => 'Recovery Code',
                'description' => 'Please confirm access to your account by entering one of your emergency recovery codes.',
                'toggle_text' => 'login using an authentication code',
                'placeholder' => 'Enter recovery code',
            ],
            'page_title' => 'Two-Factor Authentication',
            'submit' => 'Continue',
            'or_you_can' => 'or you can ',
        ],

        'verify_email' => [
            'title' => 'Verify email',
            'description' => 'Please verify your email address by clicking on the link we just emailed to you.',
            'page_title' => 'Email verification',
            'link_sent' => 'A new verification link has been sent to the email address you provided during registration.',
            'resend' => 'Resend verification email',
            'log_out' => 'Log out',
        ],
    ],

    'register' => 'Register',
    'log_in' => 'Log in',

    'navigation' => [
        'dashboard' => 'Dashboard',
    ],

    'pricing' => [
        'heading' => 'Pricing Plans',
        'description' => 'Choose the perfect plan for your needs',
        'credit_cta' => 'Buy Credits',
        'subscribe_cta' => 'Subscribe',
        'most_popular' => 'Most popular',
        'monthly' => 'month',
    ],

    'settings' => [
        'layout' => [
            'heading' => 'Settings',
            'description' => 'Manage your profile and account settings',
            'nav' => [
                'profile' => 'Profile',
                'password' => 'Password',
                'two_factor' => 'Two-Factor Auth',
                'appearance' => 'Appearance',
            ],
        ],
        'common' => [
            'saved' => 'Saved',
        ],

        'appearance' => [
            'page_title' => 'Appearance settings',
            'heading' => 'Appearance settings',
            'description' => "Update your account's appearance settings",
        ],

        'profile' => [
            'page_title' => 'Profile settings',
            'heading' => 'Profile information',
            'description' => 'Update your name and email address',
            'name_label' => 'Name',
            'name_placeholder' => 'Full name',
            'email_label' => 'Email address',
            'email_placeholder' => 'Email address',
            'unverified_notice' => 'Your email address is unverified.',
            'resend_cta' => 'Click here to resend the verification email.',
            'verification_link_sent' => 'A new verification link has been sent to your email address.',
            'save_button' => 'Save',
        ],

        'password' => [
            'page_title' => 'Password settings',
            'heading' => 'Update password',
            'description' => 'Ensure your account is using a long, random password to stay secure',
            'current_password_label' => 'Current password',
            'current_password_placeholder' => 'Current password',
            'new_password_label' => 'New password',
            'new_password_placeholder' => 'New password',
            'password_confirmation_label' => 'Confirm password',
            'password_confirmation_placeholder' => 'Confirm password',
            'save_button' => 'Save password',
        ],

        'two_factor' => [
            'page_title' => 'Two-Factor Authentication',
            'heading' => 'Two-Factor Authentication',
            'description' => 'Manage your two-factor authentication settings',
            'enabled_badge' => 'Enabled',
            'enabled_paragraph' => 'With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.',
            'disable_button' => 'Disable 2FA',
            'disabled_badge' => 'Disabled',
            'disabled_paragraph' => 'When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.',
            'continue_setup' => 'Continue Setup',
            'enable_button' => 'Enable 2FA',
        ],
    ],
];
