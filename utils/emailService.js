// const nodemailer = require('nodemailer');

// // Create transporter
// const transporter = nodemailer.createTransport({
//   host: process.env.EMAIL_HOST,
//   port: process.env.EMAIL_PORT,
//   secure: false,
//   auth: {
//     user: process.env.EMAIL_USER,
//     pass: process.env.EMAIL_PASS
//   }
// });

// // Send email verification
// exports.sendVerificationEmail = async (user, token) => {
//   const verificationUrl = `${process.env.FRONTEND_URL}/verify-email?token=${token}`;
  
//   const mailOptions = {
//     from: process.env.EMAIL_FROM,
//     to: user.email,
//     subject: 'Verify Your Email - UEW Digital Library',
//     html: `
//       <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
//         <h2>Welcome to UEW Digital Library!</h2>
//         <p>Hi ${user.firstName},</p>
//         <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
//         <div style="text-align: center; margin: 30px 0;">
//           <a href="${verificationUrl}" 
//              style="background-color: #4CAF50; color: white; padding: 12px 30px; 
//                     text-decoration: none; border-radius: 5px; display: inline-block;">
//             Verify Email
//           </a>
//         </div>
//         <p>Or copy and paste this link in your browser:</p>
//         <p style="word-break: break-all; color: #666;">${verificationUrl}</p>
//         <p>This link will expire in 24 hours.</p>
//         <p>If you didn't create this account, please ignore this email.</p>
//         <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
//         <p style="color: #666; font-size: 12px;">
//           UEW School of Business - Digital Library<br>
//           This is an automated email, please do not reply.
//         </p>
//       </div>
//     `
//   };

//   try {
//     await transporter.sendMail(mailOptions);
//     console.log('Verification email sent to:', user.email);
//   } catch (error) {
//     console.error('Error sending verification email:', error);
//     throw error;
//   }
// };

// // Send password reset email
// exports.sendPasswordResetEmail = async (user, token) => {
//   const resetUrl = `${process.env.FRONTEND_URL}/reset-password?token=${token}`;
  
//   const mailOptions = {
//     from: process.env.EMAIL_FROM,
//     to: user.email,
//     subject: 'Password Reset Request - UEW Digital Library',
//     html: `
//       <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
//         <h2>Password Reset Request</h2>
//         <p>Hi ${user.firstName},</p>
//         <p>We received a request to reset your password. Click the button below to create a new password:</p>
//         <div style="text-align: center; margin: 30px 0;">
//           <a href="${resetUrl}" 
//              style="background-color: #2196F3; color: white; padding: 12px 30px; 
//                     text-decoration: none; border-radius: 5px; display: inline-block;">
//             Reset Password
//           </a>
//         </div>
//         <p>Or copy and paste this link in your browser:</p>
//         <p style="word-break: break-all; color: #666;">${resetUrl}</p>
//         <p>This link will expire in 1 hour.</p>
//         <p><strong>If you didn't request this, please ignore this email and your password will remain unchanged.</strong></p>
//         <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
//         <p style="color: #666; font-size: 12px;">
//           UEW School of Business - Digital Library<br>
//           This is an automated email, please do not reply.
//         </p>
//       </div>
//     `
//   };

//   try {
//     await transporter.sendMail(mailOptions);
//     console.log('Password reset email sent to:', user.email);
//   } catch (error) {
//     console.error('Error sending password reset email:', error);
//     throw error;
//   }
// };

// // Send notification email
// exports.sendNotificationEmail = async (user, notification) => {
//   const mailOptions = {
//     from: process.env.EMAIL_FROM,
//     to: user.email,
//     subject: notification.title,
//     html: `
//       <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
//         <h2>${notification.title}</h2>
//         <p>Hi ${user.firstName},</p>
//         <p>${notification.message}</p>
//         ${notification.link ? `
//           <div style="text-align: center; margin: 30px 0;">
//             <a href="${notification.link}" 
//                style="background-color: #4CAF50; color: white; padding: 12px 30px; 
//                       text-decoration: none; border-radius: 5px; display: inline-block;">
//               View Details
//             </a>
//           </div>
//         ` : ''}
//         <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
//         <p style="color: #666; font-size: 12px;">
//           UEW School of Business - Digital Library<br>
//           To manage your notification preferences, log in to your account.
//         </p>
//       </div>
//     `
//   };

//   try {
//     if (user.preferences && user.preferences.emailNotifications) {
//       await transporter.sendMail(mailOptions);
//       console.log('Notification email sent to:', user.email);
//     }
//   } catch (error) {
//     console.error('Error sending notification email:', error);
//   }
// };



const nodemailer = require('nodemailer');

// Create transporter (Gmail SMTP - STARTTLS on 587)
const transporter = nodemailer.createTransport({
  host: process.env.EMAIL_HOST,                 // smtp.gmail.com
  port: Number(process.env.EMAIL_PORT),         // 587
  secure: false,                                // MUST be false for 587
  auth: {
    user: process.env.EMAIL_USER,
    pass: process.env.EMAIL_PASS
  },
  requireTLS: true,                             // force STARTTLS
  tls: {
    servername: process.env.EMAIL_HOST
  },
  connectionTimeout: 20000,
  greetingTimeout: 20000,
  socketTimeout: 20000
});

// (Optional) verify SMTP on startup - helps debug
transporter.verify()
  .then(() => console.log('✅ SMTP ready'))
  .catch((err) => console.error('❌ SMTP verify failed:', err));

// Send email verification
exports.sendVerificationEmail = async (user, token) => {
  const verificationUrl = `${process.env.FRONTEND_URL}/verify-email?token=${token}`;
  
  const mailOptions = {
    from: process.env.EMAIL_FROM,
    to: user.email,
    subject: 'Verify Your Email - UEW Digital Library',
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>Welcome to UEW Digital Library!</h2>
        <p>Hi ${user.firstName},</p>
        <p>Thank you for registering. Please verify your email address by clicking the button below:</p>
        <div style="text-align: center; margin: 30px 0;">
          <a href="${verificationUrl}" 
             style="background-color: #4CAF50; color: white; padding: 12px 30px; 
                    text-decoration: none; border-radius: 5px; display: inline-block;">
            Verify Email
          </a>
        </div>
        <p>Or copy and paste this link in your browser:</p>
        <p style="word-break: break-all; color: #666;">${verificationUrl}</p>
        <p>This link will expire in 24 hours.</p>
        <p>If you didn't create this account, please ignore this email.</p>
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        <p style="color: #666; font-size: 12px;">
          UEW School of Business - Digital Library<br>
          This is an automated email, please do not reply.
        </p>
      </div>
    `
  };

  try {
    await transporter.sendMail(mailOptions);
    console.log('Verification email sent to:', user.email);
  } catch (error) {
    console.error('Error sending verification email:', error);
    throw error;
  }
};

// Send password reset email
exports.sendPasswordResetEmail = async (user, token) => {
  const resetUrl = `${process.env.FRONTEND_URL}/reset-password?token=${token}`;
  
  const mailOptions = {
    from: process.env.EMAIL_FROM,
    to: user.email,
    subject: 'Password Reset Request - UEW Digital Library',
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>Password Reset Request</h2>
        <p>Hi ${user.firstName},</p>
        <p>We received a request to reset your password. Click the button below to create a new password:</p>
        <div style="text-align: center; margin: 30px 0;">
          <a href="${resetUrl}" 
             style="background-color: #2196F3; color: white; padding: 12px 30px; 
                    text-decoration: none; border-radius: 5px; display: inline-block;">
            Reset Password
          </a>
        </div>
        <p>Or copy and paste this link in your browser:</p>
        <p style="word-break: break-all; color: #666;">${resetUrl}</p>
        <p>This link will expire in 1 hour.</p>
        <p><strong>If you didn't request this, please ignore this email and your password will remain unchanged.</strong></p>
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        <p style="color: #666; font-size: 12px;">
          UEW School of Business - Digital Library<br>
          This is an automated email, please do not reply.
        </p>
      </div>
    `
  };

  try {
    await transporter.sendMail(mailOptions);
    console.log('Password reset email sent to:', user.email);
  } catch (error) {
    console.error('Error sending password reset email:', error);
    throw error;
  }
};

// Send notification email
exports.sendNotificationEmail = async (user, notification) => {
  const mailOptions = {
    from: process.env.EMAIL_FROM,
    to: user.email,
    subject: notification.title,
    html: `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>${notification.title}</h2>
        <p>Hi ${user.firstName},</p>
        <p>${notification.message}</p>
        ${notification.link ? `
          <div style="text-align: center; margin: 30px 0;">
            <a href="${notification.link}" 
               style="background-color: #4CAF50; color: white; padding: 12px 30px; 
                      text-decoration: none; border-radius: 5px; display: inline-block;">
              View Details
            </a>
          </div>
        ` : ''}
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        <p style="color: #666; font-size: 12px;">
          UEW School of Business - Digital Library<br>
          To manage your notification preferences, log in to your account.
        </p>
      </div>
    `
  };

  try {
    if (user.preferences && user.preferences.emailNotifications) {
      await transporter.sendMail(mailOptions);
      console.log('Notification email sent to:', user.email);
    }
  } catch (error) {
    console.error('Error sending notification email:', error);
  }
};
