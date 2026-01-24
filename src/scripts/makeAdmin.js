// import admin from "firebase-admin";
// import path from "path";
// import fs from "fs";

// const keyPath = path.resolve("firebase-service-account.json");
// if (!fs.existsSync(keyPath)) {
//   throw new Error("Missing firebase-service-account.json in project root");
// }

// admin.initializeApp({
//   credential: admin.credential.cert(keyPath),
// });

// const uid = process.argv[2];
// if (!uid) {
//   console.log("Usage: node src/scripts/makeAdmin.js <UID>");
//   process.exit(1);
// }

// await admin.auth().setCustomUserClaims(uid, { admin: true });

// console.log(`✅ Admin claim set for UID: ${uid}`);


// process.exit(0);


import admin from "../config/firebaseAdmin.js";

const email = process.argv[2];

if (!email) {
  console.error("Usage: node src/scripts/makeAdmin.js someone@gmail.com");
  process.exit(1);
}

async function run() {
  const user = await admin.auth().getUserByEmail(email);
  await admin.auth().setCustomUserClaims(user.uid, { admin: true });
  console.log(`✅ ${email} is now an admin`);
  process.exit(0);
}

run().catch((e) => {
  console.error("Error:", e.message);
  process.exit(1);
});
