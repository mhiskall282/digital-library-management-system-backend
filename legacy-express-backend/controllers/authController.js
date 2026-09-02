const jwt = require('jsonwebtoken');
const User = require('../models/User');
const crypto = require('crypto');
const PasswordReset = require('../models/PasswordReset');
const emailService = require('../utils/emailService');


// Add these functions to your existing authController.js

// @desc    Resend verification email
// @route   POST /api/auth/resend-verification
// @access  Private
exports.resendVerification = async (req, res) => {
  try {
    const user = await User.findById(req.user._id);

    if (user.isEmailVerified) {
      return res.status(400).json({ message: 'Email already verified' });
    }

    // Generate verification token
    const verificationToken = crypto.randomBytes(32).toString('hex');
    
    user.emailVerificationToken = verificationToken;
    user.emailVerificationExpires = Date.now() + 24 * 60 * 60 * 1000; // 24 hours
    await user.save();

    // Send verification email
    await emailService.sendVerificationEmail(user, verificationToken);

    res.json({ message: 'Verification email sent successfully' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Verify email
// @route   GET /api/auth/verify-email/:token
// @access  Public
exports.verifyEmail = async (req, res) => {
  try {
    const user = await User.findOne({
      emailVerificationToken: req.params.token,
      emailVerificationExpires: { $gt: Date.now() }
    });

    if (!user) {
      return res.status(400).json({ 
        message: 'Invalid or expired verification token' 
      });
    }

    user.isEmailVerified = true;
    user.emailVerificationToken = undefined;
    user.emailVerificationExpires = undefined;
    await user.save();

    res.json({ 
      message: 'Email verified successfully',
      user: {
        _id: user._id,
        email: user.email,
        isEmailVerified: user.isEmailVerified
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Request password reset
// @route   POST /api/auth/forgot-password
// @access  Public
exports.forgotPassword = async (req, res) => {
  try {
    const { email } = req.body;

    const user = await User.findOne({ email });

    if (!user) {
      // Don't reveal if user exists
      return res.json({ 
        message: 'If that email exists, a password reset link has been sent' 
      });
    }

    // Generate reset token
    const resetToken = crypto.randomBytes(32).toString('hex');

    // Save reset token to database
    await PasswordReset.create({
      user: user._id,
      token: resetToken,
      expiresAt: Date.now() + 3600000 // 1 hour
    });

    // Send reset email
    await emailService.sendPasswordResetEmail(user, resetToken);

    res.json({ 
      message: 'If that email exists, a password reset link has been sent' 
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Reset password
// @route   POST /api/auth/reset-password/:token
// @access  Public
exports.resetPassword = async (req, res) => {
  try {
    const { password } = req.body;

    // Find valid reset token
    const passwordReset = await PasswordReset.findOne({
      token: req.params.token,
      expiresAt: { $gt: Date.now() },
      used: false
    }).populate('user');

    if (!passwordReset) {
      return res.status(400).json({ 
        message: 'Invalid or expired reset token' 
      });
    }

    // Update password
    const user = passwordReset.user;
    user.password = password;
    await user.save();

    // Mark token as used
    passwordReset.used = true;
    await passwordReset.save();

    res.json({ 
      message: 'Password reset successfully. You can now login with your new password.' 
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Update user preferences
// @route   PUT /api/auth/preferences
// @access  Private
exports.updatePreferences = async (req, res) => {
  try {
    const user = await User.findById(req.user._id);

    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    // Update preferences
    if (req.body.emailNotifications !== undefined) {
      user.preferences.emailNotifications = req.body.emailNotifications;
    }
    if (req.body.newResourceAlerts !== undefined) {
      user.preferences.newResourceAlerts = req.body.newResourceAlerts;
    }

    await user.save();

    res.json({
      message: 'Preferences updated successfully',
      preferences: user.preferences
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// Generate JWT token
const generateToken = (id) => {
  return jwt.sign({ id }, process.env.JWT_SECRET, {
    expiresIn: '30d'
  });
};

// @desc    Register new user
// @route   POST /api/auth/register
// @access  Public
exports.register = async (req, res) => {
  try {
    const { studentId, email, password, firstName, lastName, level, program } = req.body;

    // Check if user exists
    const userExists = await User.findOne({ $or: [{ email }, { studentId }] });

    if (userExists) {
      return res.status(400).json({ 
        message: 'User already exists with this email or student ID' 
      });
    }

    // Generate verification token
    const verificationToken = crypto.randomBytes(32).toString('hex');

    // Create user
    const user = await User.create({
      studentId,
      email,
      password,
      firstName,
      lastName,
      level,
      program,
      emailVerificationToken: verificationToken,
      emailVerificationExpires: Date.now() + 24 * 60 * 60 * 1000 // 24 hours
    });

    if (user) {
      // Send verification email
      try {
        await emailService.sendVerificationEmail(user, verificationToken);
      } catch (emailError) {
        console.error('Error sending verification email:', emailError);
        // Continue even if email fails
      }

      res.status(201).json({
        _id: user._id,
        studentId: user.studentId,
        email: user.email,
        firstName: user.firstName,
        lastName: user.lastName,
        level: user.level,
        program: user.program,
        role: user.role,
        isEmailVerified: user.isEmailVerified,
        token: generateToken(user._id),
        message: 'Registration successful. Please check your email to verify your account.'
      });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Login user
// @route   POST /api/auth/login
// @access  Public
exports.login = async (req, res) => {
  try {
    const { email, password } = req.body;

    // Check if user exists
    const user = await User.findOne({ email });

    if (!user) {
      return res.status(401).json({ message: 'Invalid email or password' });
    }

    // Check if account is active
    if (!user.isActive) {
      return res.status(401).json({ message: 'Account is deactivated' });
    }

    // Check password
    const isMatch = await user.comparePassword(password);

    if (!isMatch) {
      return res.status(401).json({ message: 'Invalid email or password' });
    }

    res.json({
      _id: user._id,
      studentId: user.studentId,
      email: user.email,
      firstName: user.firstName,
      lastName: user.lastName,
      level: user.level,
      program: user.program,
      role: user.role,
      token: generateToken(user._id)
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get current user profile
// @route   GET /api/auth/me
// @access  Private
exports.getMe = async (req, res) => {
  try {
    const user = await User.findById(req.user._id).select('-password');
    res.json(user);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Update user profile
// @route   PUT /api/auth/profile
// @access  Private
exports.updateProfile = async (req, res) => {
  try {
    const user = await User.findById(req.user._id);

    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    // Update fields
    user.firstName = req.body.firstName || user.firstName;
    user.lastName = req.body.lastName || user.lastName;
    user.email = req.body.email || user.email;
    user.program = req.body.program || user.program;

    // Update password if provided
    if (req.body.password) {
      user.password = req.body.password;
    }

    const updatedUser = await user.save();

    res.json({
      _id: updatedUser._id,
      studentId: updatedUser.studentId,
      email: updatedUser.email,
      firstName: updatedUser.firstName,
      lastName: updatedUser.lastName,
      level: updatedUser.level,
      program: updatedUser.program,
      role: updatedUser.role,
      token: generateToken(updatedUser._id)
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};