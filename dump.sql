PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE Ability (id INTEGER NOT NULL, mob_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, level INTEGER NOT NULL, PRIMARY KEY(id));
INSERT INTO "Ability" VALUES(1,1,'berserk',1);
INSERT INTO "Ability" VALUES(2,1,'bash',1);
INSERT INTO "Ability" VALUES(3,4,'berserk',1);
INSERT INTO "Ability" VALUES(4,4,'bash',1);
INSERT INTO "Ability" VALUES(5,4,'sword',1);
CREATE TABLE Affect (id INTEGER NOT NULL, attributes_id INTEGER DEFAULT NULL, mob_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, timeout INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2A5DD6D2BAAF4009 FOREIGN KEY (attributes_id) REFERENCES Attributes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2A5DD6D216E57E11 FOREIGN KEY (mob_id) REFERENCES Mob (id) NOT DEFERRABLE INITIALLY IMMEDIATE);
INSERT INTO "Affect" VALUES(1,NULL,NULL,'stun',2);
INSERT INTO "Affect" VALUES(2,NULL,NULL,'stun',0);
INSERT INTO "Affect" VALUES(3,NULL,NULL,'stun',0);
INSERT INTO "Affect" VALUES(4,NULL,NULL,'stun',0);
INSERT INTO "Affect" VALUES(5,NULL,NULL,'stun',0);
INSERT INTO "Affect" VALUES(6,NULL,NULL,'stun',1);
INSERT INTO "Affect" VALUES(7,NULL,NULL,'stun',2);
INSERT INTO "Affect" VALUES(8,NULL,NULL,'stun',1);
INSERT INTO "Affect" VALUES(9,NULL,NULL,'stun',1);
CREATE TABLE Area (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id));
INSERT INTO "Area" VALUES(1,'Midgaard');
CREATE TABLE Attributes (id INTEGER NOT NULL, hp INTEGER NOT NULL, mana INTEGER NOT NULL, mv INTEGER NOT NULL, str INTEGER NOT NULL, int INTEGER NOT NULL, wis INTEGER NOT NULL, dex INTEGER NOT NULL, con INTEGER NOT NULL, cha INTEGER NOT NULL, hit INTEGER NOT NULL, dam INTEGER NOT NULL, acSlash INTEGER NOT NULL, acBash INTEGER NOT NULL, acPierce INTEGER NOT NULL, acMagic INTEGER NOT NULL, PRIMARY KEY(id));
INSERT INTO "Attributes" VALUES(1,20,100,100,18,12,17,11,18,13,1,2,0,10,0,0);
INSERT INTO "Attributes" VALUES(2,20,100,100,15,15,15,15,15,15,1,1,0,0,0,0);
INSERT INTO "Attributes" VALUES(3,20,100,100,15,15,15,15,15,15,1,1,0,0,0,0);
INSERT INTO "Attributes" VALUES(4,20,100,100,18,12,17,11,18,13,1,2,0,10,0,0);
CREATE TABLE Direction (id INTEGER NOT NULL, direction VARCHAR(255) NOT NULL, sourceRoom_id INTEGER DEFAULT NULL, targetRoom_id INTEGER DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_BCBB5310D4668B6A FOREIGN KEY (sourceRoom_id) REFERENCES Room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BCBB53106B93493C FOREIGN KEY (targetRoom_id) REFERENCES Room (id) NOT DEFERRABLE INITIALLY IMMEDIATE);
INSERT INTO "Direction" VALUES(1,'east',1,2);
INSERT INTO "Direction" VALUES(2,'west',2,1);
INSERT INTO "Direction" VALUES(3,'east',2,3);
INSERT INTO "Direction" VALUES(4,'west',3,2);
INSERT INTO "Direction" VALUES(5,'south',2,4);
INSERT INTO "Direction" VALUES(6,'north',4,2);
CREATE TABLE Inventory (id INTEGER NOT NULL, gold INTEGER NOT NULL, silver INTEGER NOT NULL, capacityWeight INTEGER NOT NULL, capacityCount INTEGER NOT NULL, PRIMARY KEY(id));
INSERT INTO "Inventory" VALUES(1,0,0,500,250);
INSERT INTO "Inventory" VALUES(2,0,0,500,250);
INSERT INTO "Inventory" VALUES(3,0,0,500,250);
INSERT INTO "Inventory" VALUES(4,0,0,500,250);
INSERT INTO "Inventory" VALUES(5,0,0,500,250);
INSERT INTO "Inventory" VALUES(6,0,0,500,250);
INSERT INTO "Inventory" VALUES(7,0,0,500,250);
INSERT INTO "Inventory" VALUES(8,0,0,500,250);
INSERT INTO "Inventory" VALUES(9,0,0,500,250);
INSERT INTO "Inventory" VALUES(10,0,20,500,250);
INSERT INTO "Inventory" VALUES(11,0,0,500,250);
INSERT INTO "Inventory" VALUES(12,0,0,500,250);
CREATE TABLE Item (id INTEGER NOT NULL, inventory_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, look VARCHAR(255) DEFAULT NULL, material VARCHAR(255) NOT NULL, identifiers CLOB NOT NULL, weight NUMERIC(10, 0) NOT NULL, value NUMERIC(10, 0) NOT NULL, position VARCHAR(255) DEFAULT NULL, level INTEGER NOT NULL, vNum VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_BF298A209EEA759 FOREIGN KEY (inventory_id) REFERENCES Inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE);
INSERT INTO "Item" VALUES(1,1,'a small brass key',NULL,'brass','a:3:{i:0;s:5:"small";i:1;s:5:"brass";i:2;s:3:"key";}',0.1,20,'',1,'9c4b540e-d4b4-4b4f-9a5e-a356a4551e26');
INSERT INTO "Item" VALUES(2,5,'a loaf of bread',NULL,'food','a:2:{i:0;s:4:"loaf";i:1;s:5:"bread";}',0.2,4,'',1,'e39b41cd-69e5-4ec3-8d0f-3b27677ce22a');
INSERT INTO "Item" VALUES(3,3,'a copper teapot',NULL,'copper','a:2:{i:0;s:6:"copper";i:1;s:6:"teapot";}',0.5,0,'',1,'bcc89181-b70b-4370-8883-d11006034b33');
INSERT INTO "Item" VALUES(4,3,'a wooden sword',NULL,'wood','a:2:{i:0;s:6:"wooden";i:1;s:5:"sword";}',4,0,'wielded',1,'ff5c8d1b-6d76-4ffb-85e2-1ed6fad8d4be');
INSERT INTO "Item" VALUES(5,3,'a wooden mace',NULL,'wood','a:2:{i:0;s:6:"wooden";i:1;s:4:"mace";}',5,0,'wielded',1,'64befce1-aea0-40ef-a394-e3a866650432');
CREATE TABLE Mob (id INTEGER NOT NULL, room_id INTEGER DEFAULT NULL, attributes_id INTEGER DEFAULT NULL, inventory_id INTEGER DEFAULT NULL, equipped_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, look VARCHAR(255) DEFAULT NULL, disposition VARCHAR(255) NOT NULL, identifiers CLOB NOT NULL, race VARCHAR(255) NOT NULL, hp INTEGER NOT NULL, mana INTEGER NOT NULL, mv INTEGER NOT NULL, isPlayer BOOLEAN NOT NULL, gender VARCHAR(255) DEFAULT NULL, experience INTEGER NOT NULL, level INTEGER NOT NULL, debitLevels INTEGER NOT NULL, ageInSeconds INTEGER NOT NULL, roles CLOB NOT NULL, trains INTEGER NOT NULL, practices INTEGER NOT NULL, skillPoints INTEGER NOT NULL, job VARCHAR(255) NOT NULL, alignment INTEGER NOT NULL, creationPoints INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_C6DAB09D54177093 FOREIGN KEY (room_id) REFERENCES Room (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6DAB09DBAAF4009 FOREIGN KEY (attributes_id) REFERENCES Attributes (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6DAB09D9EEA759 FOREIGN KEY (inventory_id) REFERENCES Inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C6DAB09DBDC3019B FOREIGN KEY (equipped_id) REFERENCES Inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE);
INSERT INTO "Mob" VALUES(1,1,1,1,2,'A dwarven armorer','A stout dwarf totters around, stinking up the place.','standing','a:3:{i:0;s:1:"A";i:1;s:7:"dwarven";i:2;s:7:"armorer";}','dwarf',20,100,100,0,'neutral',0,1,0,1482544959,'a:1:{i:0;s:10:"shopkeeper";}',0,0,0,'uninitiated',0,9);
INSERT INTO "Mob" VALUES(2,2,2,3,4,'a janitor',NULL,'standing','a:2:{i:0;s:1:"a";i:1;s:7:"janitor";}','human',20,100,100,0,'neutral',0,1,0,1482544959,'a:2:{i:0;s:9:"scavenger";i:1;s:6:"mobile";}',0,0,0,'uninitiated',0,5);
INSERT INTO "Mob" VALUES(3,3,3,5,6,'a baker','standing behind the counter, %s wipes flour from his forehead.','standing','a:2:{i:0;s:1:"a";i:1;s:5:"baker";}','human',20,100,100,0,'neutral',0,1,0,1482544959,'a:1:{i:0;s:10:"shopkeeper";}',0,0,0,'uninitiated',0,5);
INSERT INTO "Mob" VALUES(4,2,4,10,11,'dan',NULL,'standing','a:1:{i:0;s:3:"dan";}','dwarf',20,100,100,1,'neutral',2058,2,1,1482565030,'a:0:{}',0,0,0,'warrior',0,9);
CREATE TABLE Room (id INTEGER NOT NULL, inventory_id INTEGER DEFAULT NULL, area_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, regenRate DOUBLE PRECISION NOT NULL, isOutside BOOLEAN NOT NULL, visibility INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_D2ADFEA59EEA759 FOREIGN KEY (inventory_id) REFERENCES Inventory (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2ADFEA5BD0F409C FOREIGN KEY (area_id) REFERENCES Area (id) NOT DEFERRABLE INITIALLY IMMEDIATE);
INSERT INTO "Room" VALUES(1,9,1,'Arms and Armour','  A cramped armory is filled with cheap but sturdy training equipment. A red-hot forge
and workshop consume the back half of the already small space. A silhouette of a dwarf
can be seen in front of the forge, hammering out new weapons and armor.',0.1,1,0);
INSERT INTO "Room" VALUES(2,8,1,'Midgaard Town Center','Before you is the town center.',0.1,1,0);
INSERT INTO "Room" VALUES(3,7,1,'A bakery','  A bakery shop is here.',0.1,1,0);
INSERT INTO "Room" VALUES(4,12,1,'Midgaard Commons','Standing at the center of a large square, you can see shops and people moving in all directions.',0.1,1,0);
CREATE INDEX IDX_FA72D7A016E57E11 ON Ability (mob_id);
CREATE UNIQUE INDEX UNIQ_2A5DD6D2BAAF4009 ON Affect (attributes_id);
CREATE INDEX IDX_2A5DD6D216E57E11 ON Affect (mob_id);
CREATE INDEX IDX_BCBB5310D4668B6A ON Direction (sourceRoom_id);
CREATE INDEX IDX_BCBB53106B93493C ON Direction (targetRoom_id);
CREATE INDEX IDX_BF298A209EEA759 ON Item (inventory_id);
CREATE INDEX vnum_idx ON Item (vNum);
CREATE INDEX IDX_C6DAB09D54177093 ON Mob (room_id);
CREATE UNIQUE INDEX UNIQ_C6DAB09DBAAF4009 ON Mob (attributes_id);
CREATE UNIQUE INDEX UNIQ_C6DAB09D9EEA759 ON Mob (inventory_id);
CREATE UNIQUE INDEX UNIQ_C6DAB09DBDC3019B ON Mob (equipped_id);
CREATE UNIQUE INDEX UNIQ_D2ADFEA59EEA759 ON Room (inventory_id);
CREATE INDEX IDX_D2ADFEA5BD0F409C ON Room (area_id);
COMMIT;
