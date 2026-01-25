import admin from "firebase-admin";
import fs from "fs";
import path from "path";

const keyPath = path.resolve("firebase-service-account.json");

if (!fs.existsSync(keyPath)) {
  throw new Error("Missing firebase-service-account.json in project root. Add it and restart.");
}

if (!admin.apps.length) {
  admin.initializeApp({
    credential: admin.credential.cert(keyPath),
  });
}

export const db = admin.firestore();
export default admin;
