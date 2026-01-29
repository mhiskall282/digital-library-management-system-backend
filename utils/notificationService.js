const Notification = require('../models/Notification');
const User = require('../models/User');
const emailService = require('./emailService');

// Create notification for specific user
exports.createNotification = async (userId, notificationData) => {
  try {
    const notification = await Notification.create({
      user: userId,
      ...notificationData
    });

    // Send email if user has email notifications enabled
    const user = await User.findById(userId);
    if (user && user.preferences.emailNotifications) {
      await emailService.sendNotificationEmail(user, notification);
    }

    return notification;
  } catch (error) {
    console.error('Error creating notification:', error);
    throw error;
  }
};

// Create notification for all users of a specific level
exports.notifyUsersByLevel = async (level, notificationData) => {
  try {
    const users = await User.find({ level, isActive: true });
    
    const notifications = await Promise.all(
      users.map(user => 
        Notification.create({
          user: user._id,
          ...notificationData
        })
      )
    );

    // Send emails
    for (const user of users) {
      if (user.preferences && user.preferences.emailNotifications && user.preferences.newResourceAlerts) {
        await emailService.sendNotificationEmail(user, notificationData);
      }
    }

    return notifications;
  } catch (error) {
    console.error('Error notifying users by level:', error);
    throw error;
  }
};

// Create notification for all users
exports.notifyAllUsers = async (notificationData) => {
  try {
    const users = await User.find({ isActive: true });
    
    const notifications = await Promise.all(
      users.map(user => 
        Notification.create({
          user: user._id,
          ...notificationData
        })
      )
    );

    return notifications;
  } catch (error) {
    console.error('Error notifying all users:', error);
    throw error;
  }
};