const express = require('express');
const dotenv = require('dotenv');
const cors = require('cors');
const connectDB = require('./config/db');
const { apiLimiter } = require('./middleware/rateLimiter');

// Load environment variables
dotenv.config();

// Connect to database
connectDB();

const app = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Apply rate limiting to all API routes
app.use('/api/', apiLimiter);

// Welcome route
app.get('/', (req, res) => {
  res.json({
    message: 'Welcome to UEW School of Business Digital Library API',
    version: '2.0.0',
    features: [
      'User Authentication with Email Verification',
      'Password Reset',
      'Resource Management',
      'Reviews & Ratings',
      'Bookmarks',
      'Notifications',
      'Advanced Search',
      'Multiple File Upload',
      'Admin Analytics Dashboard'
    ],
    endpoints: {
      auth: '/api/auth',
      resources: '/api/resources',
      users: '/api/users',
      reviews: '/api/reviews',
      bookmarks: '/api/bookmarks',
      notifications: '/api/notifications',
      analytics: '/api/analytics'
    }
  });
});

// Routes
app.use('/api/auth', require('./routes/auth'));
app.use('/api/resources', require('./routes/resources'));
app.use('/api/users', require('./routes/users'));
app.use('/api/reviews', require('./routes/reviews'));
app.use('/api/bookmarks', require('./routes/bookmarks'));
app.use('/api/notifications', require('./routes/notifications'));
app.use('/api/analytics', require('./routes/analytics'));

// Error handler middleware
app.use((err, req, res, next) => {
  console.error(err.stack);
  
  // Multer errors
  if (err.name === 'MulterError') {
    if (err.code === 'LIMIT_FILE_SIZE') {
      return res.status(400).json({ message: 'File size too large. Maximum 10MB allowed.' });
    }
    if (err.code === 'LIMIT_FILE_COUNT') {
      return res.status(400).json({ message: 'Too many files. Maximum 10 files allowed.' });
    }
  }

  res.status(500).json({
    message: 'Something went wrong!',
    error: process.env.NODE_ENV === 'development' ? err.message : {}
  });
});

// 404 handler
app.use((req, res) => {
  res.status(404).json({ message: 'Route not found' });
});

const PORT = process.env.PORT || 5000;

app.listen(PORT, () => {
  console.log(`
╔════════════════════════════════════════════════════════════╗
║                                                            ║
║   UEW Digital Library Backend API                         ║
║   Version: 2.0.0                                          ║
║                                                            ║
║   Server running in ${process.env.NODE_ENV} mode                        ║
║   Port: ${PORT}                                            ║
║                                                            ║
║   Features:                                               ║
║   ✓ Email Verification                                    ║
║   ✓ Password Reset                                        ║
║   ✓ Reviews & Ratings                                     ║
║   ✓ Bookmarks                                             ║
║   ✓ Notifications                                         ║
║   ✓ Advanced Search                                       ║
║   ✓ Multiple File Upload                                  ║
║   ✓ Admin Analytics                                       ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝
  `);
});

module.exports = app;