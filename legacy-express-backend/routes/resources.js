const express = require('express');
const router = express.Router();
const {
  uploadResource,
  uploadMultipleResources,
  getResources,
  getResource,
  downloadResource,
  deleteResource,
  createCategory,
  getCategories,
  advancedSearch
} = require('../controllers/resourceController');
const { protect, admin } = require('../middleware/auth');
const upload = require('../middleware/upload');

// Category routes
router.post('/categories', protect, admin, createCategory);
router.get('/categories', protect, getCategories);

// Search routes
router.get('/search/advanced', protect, advancedSearch);

// Resource routes
router.post('/upload', protect, admin, upload.single, uploadResource);
router.post('/upload-multiple', protect, admin, upload.multiple, uploadMultipleResources);
router.get('/', protect, getResources);
router.get('/:id', protect, getResource);
router.get('/:id/download', protect, downloadResource);
router.delete('/:id', protect, admin, deleteResource);

module.exports = router;