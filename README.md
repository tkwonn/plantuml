# PlantUML Server

[![GitHub last commit](https://img.shields.io/github/last-commit/tkwonn/plantuml?color=chocolate)](https://github.com/tkwonn/plantuml/commits/)
[![deploy to EC2](https://github.com/tkwonn/plantuml/actions/workflows/deploy.yml/badge.svg)](https://github.com/tkwonn/plantuml/actions/workflows/deploy.yml)

<br>

## What is this

A web-based UML learning tool that helps users practice creating various types of UML diagrams using PlantUML syntax.

**URL**: [plantuml.taesokkwon.com](https://plantuml.taesokkwon.com)

<br>

## Table of Contents

1. [Demo](#demo)
2. [Built with](#built-with)
3. [Features](#features)
4. [Security Measures](#security-measures)
4. [CI/CD](#cicd)
   - [CI](#ci)
      - [Dependency Caching](#dependency-caching)
      - [Code Quality Check](#code-quality-check)
   - [CD](#cd)
      - [OpenID Connect for Secure AWS Authentication](#openid-connect-for-secure-aws-authentication)
      - [AWS Systems Manager (SSM)](#aws-systems-manager-ssm)

<br>

## Demo

Step1. Select a problem from the list and write UML code in the editor while comparing with the example solution.

https://github.com/user-attachments/assets/315e98bf-2a4e-4f0c-9b79-06c349c60040

Step2. Download the diagram.

https://github.com/user-attachments/assets/e0c59a51-8b2c-4434-9370-fc0541a7b3a0

<br>

## Built with

| **Category**      | **Technology**                                                                                                                                               |
|-------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| VM                | Amazon EC2                                                                                                                                                   |
| Web server        | Nginx                                                                                                                                                        |
| Frontend          | HTML, JavaScript, Bootstrap CSS                                                                                                                              |
| Backend           | PHP                                                                                                                                                          |
| Storage           | Temporary file storage on server                                                                                                                             |
| CI/CD             | GitHub Actions                                                                                                                                               |
| Framework & Tools | - Monaco editor (code editor)<br>- [PlantUML v1.2024.7 (UML diagram generation)](https://plantuml.com/download)<br>- Graphviz (graph visualization software) |

<br>

## Features

The application features a three-pane interface:

1. Editor Pane: Code editor supporting various UML diagrams (Use Case, Class, Activity, etc.)
2. Preview Pane: Real-time diagram rendering
3. Solution Pane: Interactive cheat sheets which allows user to understand PlantUML syntax by comparing their work with example solutions

<br>

Diagrams can be exported in the following formats:
- PNG
- SVG
- TXT (for source code backup and reuse)

<br>

## Security Measures


| **Category** | **Description**                                                                                                                                        |
|--------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| XSS          | - HTML content escape: `htmlspecialchars()` with ENT_QUOTES flag. <br> - JavaScript string escape: `json_encode()`. <br> - URL attribute escape: `urlencode()` and URL scheme validation. |
| OS command injection | Applied `escapeshellarg()` to properly escape and quote shell arguments when executing PlantUML jar file.                                    |

<br>

## CI/CD

### CI

#### Dependency Caching

Utilizes composer cache to speed up dependency installation by reusing previously installed modules.   

#### Code Quality Check

Runs PHP CS Fixer in dry-run mode to check for coding standard violations, ensuring no unintended changes are merged.

### CD

#### OpenID Connect for Secure AWS Authentication

- Uses OpenID Connect to securely assume an IAM role with short-lived tokens, eliminating the need to manage long-term AWS credentials as secrets.
- Ensures secure cloud role operations by granting only the minimal necessary permissions (avoiding FullAccess roles) and verifying that access is restricted to the intended repository.

#### AWS Systems Manager (SSM)

- Used SSM to execute commands on the EC2 instance after authentication, removing the need for direct SSH access or changes to security groups.
- Key operations include:
    - Pulling the latest code.
    - Installing dependencies with `composer install`.
    - Restarting services (`php8.3-fpm` and `nginx`) to apply changes.


