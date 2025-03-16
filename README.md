# PlantUML Server

[![GitHub last commit](https://img.shields.io/github/last-commit/tkwonn/plantuml?color=chocolate)](https://github.com/tkwonn/plantuml/commits/)
[![CI](https://github.com/tkwonn/plantuml/actions/workflows/ci.yml/badge.svg)](https://github.com/tkwonn/plantuml/actions/workflows/ci.yml)
[![deploy to EC2](https://github.com/tkwonn/plantuml/actions/workflows/cd.yml/badge.svg)](https://github.com/tkwonn/plantuml/actions/workflows/cd.yml)

## Table of Contents
-   [About](#-about)
-   [Demo](#-demo)
-   [Built with](#️-built-with)
-   [Features](#-features)
-   [Security Measures](#️-security-measures)
-   [CI/CD](#-cicd)

## 💡 About

**PlantUML Server** is a web-based UML learning tool that helps users practice creating various types of UML diagrams using PlantUML syntax.

**URL**: [plantuml.taesokkwon.com](https://plantuml.taesokkwon.com)

## 🎨 Demo

Step1. Select a problem from the list and write UML code in the editor while comparing with the example solution.

https://github.com/user-attachments/assets/315e98bf-2a4e-4f0c-9b79-06c349c60040

Step2. Download the diagram.

https://github.com/user-attachments/assets/e0c59a51-8b2c-4434-9370-fc0541a7b3a0

## 🏗️ Built with

| **Category**      | **Technology**                                                                                                                                               |
|-------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| VM                | Amazon EC2                                                                                                                                                   |
| Web server        | Nginx                                                                                                                                                        |
| Frontend          | HTML, JavaScript, Bootstrap CSS                                                                                                                              |
| Backend           | PHP 8.3                                                                                                                                                      |
| Storage           | Temporary file storage on server                                                                                                                             |
| CI/CD             | GitHub Actions                                                                                                                                               |
| QA/Testing        | - PHP CS Fixer (code formatting) <br> - PHPStan (level 9, strictest configuration) <br> - PHPUnit (unit testing)                                             |
| Framework & Tools | - Monaco editor (code editor)<br>- [PlantUML v1.2024.7 (UML diagram generation)](https://plantuml.com/download)<br>- Graphviz (graph visualization software) |

## 🔥 Features

The application features a three-pane interface:

1. Editor Pane: Code editor supporting various UML diagrams (Use Case, Class, Activity, etc.)
2. Preview Pane: Real-time diagram rendering
3. Solution Pane: Interactive cheat sheets which allow users to understand PlantUML syntax by comparing their work with example solutions

Diagrams can be exported in the following formats:
- PNG
- SVG
- TXT (for source code backup and reuse)

## 🛡️ Security Measures

| **Category** | **Description**                                                                                                                                        |
|--------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| XSS          | - HTML content escape: `htmlspecialchars()` with ENT_QUOTES flag. <br> - JavaScript string escape: `json_encode()`. <br> - URL attribute escape: `urlencode()` and URL scheme validation. |
| OS command injection | Applied `escapeshellarg()` to properly escape and quote shell arguments when executing PlantUML jar file.                                    |

## 🚀 CI/CD

This project uses **GitHub Actions** for two separate workflows:

### Continuous Integration (CI)

Located in [`.github/workflows/ci.yml`](.github/workflows/ci.yml), the **CI** workflow runs on pushes to the `main` branch (and other specified triggers). It:

- Installs and caches dependencies via Composer
- Performs **PHP CS Fixer** checks for code style
- Performs **PHPStan** static analysis for code quality
- Runs **PHPUnit** unit tests to verify functionality

### Continuous Deployment (CD)

Located in [`.github/workflows/cd.yml`](.github/workflows/cd.yml), the **CD** workflow is triggered automatically **only if the CI workflow succeeds**. It:

- Uses GitHub Actions OpenID Connect to assume a short-lived AWS role
- Runs commands remotely via **AWS Systems Manager (SSM)** to pull and deploy changes on the EC2 instance (no direct SSH access or security group changes)
- Updates Composer dependencies on the server and restarts services (PHP-FPM, Nginx)

