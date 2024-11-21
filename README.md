# PlantUML Server

[![GitHub last commit](https://img.shields.io/github/last-commit/tkwonn/plantuml?color=chocolate)](https://github.com/tkwonn/plantuml/commits/)
[![deploy to EC2](https://github.com/tkwonn/plantuml/actions/workflows/deploy.yml/badge.svg)](https://github.com/tkwonn/plantuml/actions/workflows/deploy.yml)

## What is this

A web-based UML learning tool that helps users practice creating various types of UML diagrams using PlantUML syntax.   

**URL**: [plantuml.taesokkwon.com](https://plantuml.taesokkwon.com)

## Demo

Step1. Select a problem from the list and write UML code in the editor while comparing with the example solution.

https://github.com/user-attachments/assets/315e98bf-2a4e-4f0c-9b79-06c349c60040

Step2. Download the diagram.

https://github.com/user-attachments/assets/b464fd36-e7a3-4937-8f9b-8407adae0581

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

## Features

The application features a three-pane design that combines a code editor, a live preview, and a learning pane.

1. **Code Editor Pane**
- Built-in Monaco editor
- Support for various UML diagrams (Use Case, Class, Activity, State, Mind Map, Gantt Chart, etc.)

2. **Preview Pane**
- Live preview with instant feedback
- Real-time diagram rendering as you type

3. **Learning Pane**
- Interactive cheat sheets for UML syntax
- Allowing users to understand PlantUML syntax by comparing their work with example solutions.

### Export Options

Diagrams can be exported in the following formats:
- PNG
- SVG
- TXT (for source code backup and reuse)

### What I focused on

As non-functional requirements for this project, I focused on the following points:

(1) The application is designed to be easily extended with new UML diagram types and practice problems. New problems can be added simply by creating a JSON file with the following structure:

```json
{
 "id": 5,
 "title": "Lifeline Activation",
 "theme": "Sequence Diagram",
 "uml": "@startuml\nautoactivate on\nalice -> bob : hello\nbob -> bob : self call\nbill -> bob #005500 : hello from thread 2\nbob -> george ** : create\nreturn done in thread 2\nreturn rc\nbob -> george !! : delete\nreturn success\n@enduml"
}
```

(2) To prevent resource exhaustion, the server automatically deletes temporary files after diagram generation.

## Security Measures


| **Category** | **Description**                                                                                                                                        |
|--------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| HTML escape  | Applied `htmlspecialchars()` with ENT_QUOTES flag to safely display data from storage by encoding special characters.                                  |
| OS command   | Protected against command injection attacks by using `escapeshellarg()` to properly escape and quote shell arguments when executing PlantUML jar file. |

## CI/CD

### CI


### CD

