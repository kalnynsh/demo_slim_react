pipeline {
    agent any
    options {
        timestamps()
    }
    environment {
        CI = "true"
    }
    stages {
        stage("Init") {
            steps {
                sh -c "make init"
            }
        }
        stage("Validation") {
            steps {
                sh -c "make api-validate-schema"
            }
        }
        stage("Lint") {
            parallel {
                stage("API") {
                    steps {
                        sh -c "make api-lint"
                    }
                }
                stage("Frontend") {
                    steps {
                        sh -c "make frontend-lint"
                    }
                }
                stage("Cucumber") {
                    steps {
                        sh -c "make cucumber-lint"
                    }
                }
            }
        }
        stage("Analyze") {
            sh -c "make api-analyze"
        }
        stage("Down") {
            steps {
                sh -c "make docker-down-clear"
            }
        }
    }
    post {
        always {
            sh -c "make docker-down-clear || true"
        }
    }
}
