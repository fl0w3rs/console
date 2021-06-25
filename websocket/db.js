const { Sequelize, DataTypes } = require('sequelize');

const sequelize = new Sequelize('console', 'console', 'J2l0X1w9', {
    host: '49.12.66.136',
    dialect: 'mysql',
    define: {
        timestamps: false
    }
  });

const Session = sequelize.define('session', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        hash: {
            type: DataTypes.STRING(65)
        },
        useragent: {
            type: DataTypes.STRING(255)
        },
        ip: {
            type: DataTypes.STRING(65)
        },
        fid: {
            type: DataTypes.INTEGER
        }, 
        active: {
            type: DataTypes.INTEGER
        }
    },
    {
        tableName: 'sessions'
    }
);

const Setting = sequelize.define('setting', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        s_key: {
            type: DataTypes.STRING(65),
            unique: true
        },
        s_value: {
            type: DataTypes.STRING(255)
        },
        s_api: {
            type: DataTypes.INTEGER
        }
    },
    {
        tableName: 'settings'
    }
);

const User = sequelize.define('user', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        fid: {
            type: DataTypes.INTEGER,
            unique: true
        },
        department: {
            type: DataTypes.INTEGER
        },
        steam_id: {
            type: DataTypes.STRING(65)
        },
        muted_buttons: {
            type: DataTypes.INTEGER
        },
        notebook: {
            type: DataTypes.TEXT
        },
        name: {
            type: DataTypes.STRING(65)
        }
    },
    {
        tableName: 'users'
    }
);

const Department = sequelize.define('department', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        name: {
            type: DataTypes.STRING(65)
        },
        groups: {
            type: DataTypes.STRING(65)
        },
        training_field: {
            type: DataTypes.STRING(65)
        },
        type: {
            type: DataTypes.INTEGER
        }
    },
    {
        tableName: 'departments'
    }
);

const Situation = sequelize.define('situation', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        creator: {
            type: DataTypes.INTEGER
        },
        street: {
            type: DataTypes.STRING(65)
        },
        intersected_street: {
            type: DataTypes.STRING(65)
        },
        block: {
            type: DataTypes.STRING(65)
        },
        issuer_name: {
            type: DataTypes.STRING(65)
        },
        description: {
            type: DataTypes.TEXT
        },
        display_title: {
            type: DataTypes.STRING(65)
        },
        display_type: {
            type: DataTypes.STRING(65)
        },
        status: {
            type: DataTypes.INTEGER
        },
        code_4: {
            type: DataTypes.INTEGER
        }
    },
    {
        tableName: 'situations'
    }
);

const SituationLog = sequelize.define('situation_log', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        sit_id: {
            type: DataTypes.INTEGER
        },
        is_auto: {
            type: DataTypes.INTEGER
        },
        creator_name: {
            type: DataTypes.STRING(65)
        },
        message: {
            type: DataTypes.STRING(512)
        }
    },
    {
        tableName: 'situations_logs'
    }
);

const SituationAttached = sequelize.define('situation_attached', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        sit_id: {
            type: DataTypes.INTEGER
        },
        unit_id: {
            type: DataTypes.INTEGER
        },
        arrived: {
            type: DataTypes.INTEGER
        }
    },
    {
        tableName: 'situations_attached'
    }
);

const Character = sequelize.define('character', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        owner: {
            type: DataTypes.INTEGER
        },
        name: {
            type: DataTypes.STRING(65)
        },
    },
    {
        tableName: 'characters'
    }
);

const Unit = sequelize.define('unit', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        uid: {
            type: DataTypes.INTEGER,
            unique: true
        },
        type: {
            type: DataTypes.INTEGER
        },
        status: {
            type: DataTypes.INTEGER
        },
        dept: {
            type: DataTypes.INTEGER
        },
    },
    {
        tableName: 'units'
    }
);

const BoloPeople = sequelize.define('bolo_peoples', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        creator: {
            type: DataTypes.INTEGER,
        },
        target: {
            type: DataTypes.STRING(256)
        },
        reason: {
            type: DataTypes.STRING(256)
        },
        description: {
            type: DataTypes.TEXT
        },
        last_location: {
            type: DataTypes.STRING(256)
        },
        priority: {
            type: DataTypes.INTEGER
        },
    },
    {
        tableName: 'bolo_peoples'
    }
);

const BoloVehicle = sequelize.define('bolo_vehicle', 
    {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        creator: {
            type: DataTypes.INTEGER,
        },
        target: {
            type: DataTypes.STRING(256)
        },
        reason: {
            type: DataTypes.STRING(256)
        },
        description: {
            type: DataTypes.TEXT
        },
        last_location: {
            type: DataTypes.STRING(256)
        },
        priority: {
            type: DataTypes.INTEGER
        },
    },
    {
        tableName: 'bolo_vehicle'
    }
);
  
exports.Session = Session
exports.Setting = Setting
exports.BoloVehicle = BoloVehicle
exports.BoloPeople = BoloPeople
exports.User = User
exports.Department = Department
exports.Situation = Situation
exports.SituationLog = SituationLog
exports.SituationAttached = SituationAttached
exports.Character = Character
exports.Unit = Unit
exports.sequelize = sequelize