const jwt = require('jsonwebtoken');
const User = require('../models/User');

// Protect routes - verify token
exports.protect = async (req, res, next) => {
  let token;

  if (req.headers.authorization && req.headers.authorization.startsWith('Bearer')) {
    try {
      // Get token from header
      token = req.headers.authorization.split(' ')[1];

      // Verify token
      const decoded = jwt.verify(token, process.env.JWT_SECRET);

      // Get user from token
      req.user = await User.findById(decoded.id).select('-password');

      if (!req.user) {
        return res.status(401).json({ message: 'User not found' });
      }

      if (!req.user.isActive) {
        return res.status(401).json({ message: 'Account is deactivated' });
      }

      next();
    } catch (error) {
      console.error(error);
      return res.status(401).json({ message: 'Not authorized, token failed' });
    }
  }

  if (!token) {
    return res.status(401).json({ message: 'Not authorized, no token' });
  }
};

// Admin only middleware
exports.admin = (req, res, next) => {
  if (req.user && req.user.role === 'admin') {
    next();
  } else {
    res.status(403).json({ message: 'Access denied. Admin only.' });
  }
};

// Check if user level matches resource level
exports.checkLevel = (req, res, next) => {
  // Admin can access everything
  if (req.user.role === 'admin') {
    return next();
  }

  const resourceLevel = req.resourceLevel; // We'll set this in the controller
  const userLevel = req.user.level;

  // Define level hierarchy
  const levels = ['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'];
  const userLevelIndex = levels.indexOf(userLevel);
  const resourceLevelIndex = levels.indexOf(resourceLevel);

  if (userLevelIndex >= resourceLevelIndex) {
    next();
  } else {
    res.status(403).json({ 
      message: 'You do not have access to this resource level' 
    });
  }
};