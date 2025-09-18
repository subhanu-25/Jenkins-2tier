environment {
    APP_DIR = "/var/www/html"
    RDS_ENDPOINT = "data.cliumscw44qs.ap-south-1.rds.amazonaws.com"
    DB_USER = "subha"
    DB_NAME = "LoginDB"
}

stages {
    stage('Checkout Code') {
        steps {
            checkout scm
        }
    }

    stage('Set DB Password & Deploy') {
        steps {
            withCredentials([string(credentialsId: 'rds-password', variable: 'DB_PASS')]) {
                sh '''
                echo "Deploying app and initializing DB..."
                sudo rm -rf ${APP_DIR}/*
                sudo cp index.php config.php ${APP_DIR}/
                sudo chown -R www-data:www-data ${APP_DIR}

                mysql -h ${RDS_ENDPOINT} -u${DB_USER} -p${DB_PASS} <<EOF
                CREATE DATABASE IF NOT EXISTS ${DB_NAME};
                USE ${DB_NAME};
                CREATE TABLE IF NOT EXISTS users (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  username VARCHAR(50) NOT NULL UNIQUE,
                  password VARCHAR(255) NOT NULL
                );
EOF
                '''
            }
        }
    }
}

