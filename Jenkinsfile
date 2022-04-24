pipeline {
    agent any
    options {
        timestamps()
    }
    environment {
        CI = "true"
    }
    stages {
        stage("One") {
            steps {
                sh "Echo \"this is step 1\" && sleep(1)"
            }
        }
        stage("Two") {
            steps {
                sh "Echo \"this is step 2\" && sleep(1)"
            }
        }
        stage("Three") {
            steps {
                sh "Echo \"this is step 3\" && sleep(1)"
            }
        }
    }
}
