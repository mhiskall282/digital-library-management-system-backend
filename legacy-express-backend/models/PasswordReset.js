const mongoose = require('mongoose');
const crypto = require('crypto');

const passwordResetSchema = new mongoose.Schema({
  user: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  token: {
    type: String,
    required: true
  },
  expiresAt: {
    type: Date,
    required: true,
    default: () => Date.now() + 3600000 // 1 hour from now
  },
  used: {
    type: Boolean,
    default: false
  }
}, {
  timestamps: true
});

// Generate reset token
passwordResetSchema.statics.generateToken = function() {
  return crypto.randomBytes(32).toString('hex');
};

module.exports = mongoose.model('PasswordReset', passwordResetSchema);