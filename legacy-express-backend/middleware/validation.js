const { body, validationResult } = require('express-validator');

// Validation middleware to check for errors
exports.validate = (req, res, next) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ 
      message: 'Validation failed', 
      errors: errors.array() 
    });
  }
  next();
};

// Register validation rules
exports.registerValidation = [
  body('studentId').trim().notEmpty().withMessage('Student ID is required'),
  body('email').isEmail().normalizeEmail().withMessage('Valid email is required'),
  body('password')
    .isLength({ min: 6 })
    .withMessage('Password must be at least 6 characters')
    .matches(/\d/)
    .withMessage('Password must contain a number'),
  body('firstName').trim().notEmpty().withMessage('First name is required'),
  body('lastName').trim().notEmpty().withMessage('Last name is required'),
  body('level')
    .isIn(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])
    .withMessage('Invalid level'),
  body('program').trim().notEmpty().withMessage('Program is required')
];

// Review validation rules
exports.reviewValidation = [
  body('rating')
    .isInt({ min: 1, max: 5 })
    .withMessage('Rating must be between 1 and 5'),
  body('comment')
    .optional()
    .trim()
    .isLength({ max: 500 })
    .withMessage('Comment must not exceed 500 characters')
];