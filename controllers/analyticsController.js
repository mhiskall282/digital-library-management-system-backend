const Resource = require('../models/Resource');
const User = require('../models/User');
const Category = require('../models/Category');

// @desc    Get dashboard analytics stats
// @route   GET /api/analytics/dashboard
// @access  Private/Admin
exports.getDashboardStats = async (req, res) => {
  try {
    const totalResources = await Resource.countDocuments();
    const totalUsers = await User.countDocuments();
    const totalCategories = await Category.countDocuments();

    const downloadsAgg = await Resource.aggregate([
      { $group: { _id: null, totalDownloads: { $sum: '$downloads' } } }
    ]);

    const totalDownloads = downloadsAgg[0]?.totalDownloads || 0;

    const resourcesByLevel = await Resource.aggregate([
      { $group: { _id: '$level', count: { $sum: 1 } } },
      { $sort: { _id: 1 } }
    ]);

    const resourcesByType = await Resource.aggregate([
      { $group: { _id: '$type', count: { $sum: 1 } } }
    ]);

    res.json({
      totalResources,
      totalUsers,
      totalCategories,
      totalDownloads,
      resourcesByLevel,
      resourcesByType
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get analytics for a single resource
// @route   GET /api/analytics/resources/:id
// @access  Private/Admin
exports.getResourceStats = async (req, res) => {
  try {
    const resource = await Resource.findById(req.params.id)
      .populate('category')
      .populate('uploadedBy', 'firstName lastName email');

    if (!resource) {
      return res.status(404).json({ message: 'Resource not found' });
    }

    res.json({
      resourceId: resource._id,
      title: resource.title,
      type: resource.type,
      level: resource.level,
      downloads: resource.downloads,
      averageRating: resource.averageRating,
      totalReviews: resource.totalReviews,
      uploadedBy: resource.uploadedBy,
      category: resource.category,
      createdAt: resource.createdAt
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};

// @desc    Get analytics for a single user
// @route   GET /api/analytics/users/:id
// @access  Private/Admin
exports.getUserStats = async (req, res) => {
  try {
    const user = await User.findById(req.params.id).select('-password');

    if (!user) {
      return res.status(404).json({ message: 'User not found' });
    }

    const uploadedResources = await Resource.find({ uploadedBy: user._id });

    const totalUploads = uploadedResources.length;
    const totalDownloads = uploadedResources.reduce(
      (sum, r) => sum + (r.downloads || 0),
      0
    );

    res.json({
      userId: user._id,
      name: `${user.firstName} ${user.lastName}`,
      email: user.email,
      role: user.role,
      totalUploads,
      totalDownloads,
      resources: uploadedResources.map(r => ({
        id: r._id,
        title: r.title,
        downloads: r.downloads
      }))
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Server error', error: error.message });
  }
};
