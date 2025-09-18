pipeline {
    agent { label 'ec2' }  // ensures job runs on your EC2 agent

    environment {
        APP_DIR = "/var/www/html"
        RDS_ENDPOINT = "data.cliumscw44qs.ap-south-1.rds.amazonaws.com"
        DB_USER = "subha"
        DB_PASS = subha234('rds-password')   // stored in Jenkins credentials
        DB_NAME = "LoginDB"
    }

    stages {
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                echo "📦 Installing Apache, PHP, and MySQL client..."
                sudo apt update -y
                sudo apt install -y apache2 php libapache2-mod-php php-mysql mysql-client
                sudo systemctl enable apache2
                sudo systemctl start apache2
                '''
            }
        }

        stage('Deploy App') {
            steps {
                sh '''
                echo "🚀 Deploying PHP app..."
                sudo rm -rf ${APP_DIR}/*
                sudo cp index.php config.php ${APP_DIR}/
                sudo chown -R www-data:www-data ${APP_DIR}
                '''
            }
        }

        stage('Init Database') {
            steps {
                sh '''
                echo "🛢️ Setting up database on RDS..."
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

        stage('Smoke Test') {
            steps {
                sh '''
                echo "🌐 Running smoke test..."
                curl -s http://localhost | grep "Login & Register"
                '''
            }
        }
    }

    post {
        success {
            echo "🎉 Deployment Successful!"
        }
        failure {
            echo "❌ Deployment Failed! Check logs."
        }
    }
}
