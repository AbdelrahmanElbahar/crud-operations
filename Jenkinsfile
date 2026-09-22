pipeline {
    agent any

    stages {

        stage('Test Jenkins') {
            steps {
                echo 'Jenkins is working!'
            }
        }

        stage('Check Docker') {
            steps {
                sh 'docker --version'
                sh 'docker compose version'
            }
        }

        stage('Check Project') {
            steps {
                sh 'ls -la'
                sh 'ls -la backend'
                sh 'ls -la frontend'
            }
        }

    }
}