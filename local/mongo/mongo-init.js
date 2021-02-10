print('Start #################################################################');
db.auth('root', 'password')
db = db.getSiblingDB('rocketchat');
db.createUser(
  {
    user: 'rocketchat',
    pwd: 'rocketchat1234',
    roles: [{ role: 'readWrite', db: 'rocketchat' }],
  },
);
db.createCollection('users');

db = db.getSiblingDB('admin');
db.createUser(
  {
    user: 'oplog',
    pwd: 'oplog123',
    roles: [{ role: 'read', db: 'local' }],
  },
);
db.createCollection('users');



print('END #################################################################');