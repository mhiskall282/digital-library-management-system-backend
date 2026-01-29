const express = require('express');
const router = express.Router();
const {
  register,
  login,
  getMe,
  updateProfile,
  resendVerification,
  verifyEmail,
  forgotPassword,
  resetPassword,
  updatePreferences
} = require('../controllers/authController');
const { protect } = require('../middleware/auth');
const { registerValidation, validate } = require('../middleware/validation');
const { authLimiter, emailLimiter } = require('../middleware/rateLimiter');

// Public routes
router.post('/register', registerValidation, validate, register);
router.post('/login', authLimiter, login);
router.get('/verify-email/:token', verifyEmail);
router.post('/forgot-password', emailLimiter, forgotPassword);
router.post('/reset-password/:token', resetPassword);

// Protected routes
router.get('/me', protect, getMe);
router.put('/profile', protect, updateProfile);
router.post('/resend-verification', protect, emailLimiter, resendVerification);
router.put('/preferences', protect, updatePreferences);

module.exports = router;