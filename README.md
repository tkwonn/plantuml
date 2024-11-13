## What is this

A web-based UML learning tool that helps users practice creating various types of UML diagrams using PlantUML syntax.   
The application features a three-pane interface with a code editor, live preview, and learning materials.

URL:

## Demo

The video demonstrates two main features:

1. **Problem Selection and Practice**
- Select a problem from the curated list
- Write UML code in the editor while viewing solution diagrams

video 1

2. **Diagram Export**

Download created diagrams in multiple formats.

video 2


## Built with

| **Category**      | **Technology**                                                                                                              |
|-------------------|-----------------------------------------------------------------------------------------------------------------------------|
| VM                | Amazon EC2                                                                                                                  |
| Web server        | Nginx                                                                                                                       |
| Frontend          | HTML, JavaScript, Bootstrap CSS                                                                                             |
| Backend           | PHP                                                                                                                         |
| Storage           | Temporary file storage on server                                                                                            |
| Framework & Tools | - Monaco editor (code editor)<br>- PlantUML v1.2024.7 (UML diagram generation)<br>- Graphviz (graph visualization software) |

## Features

The application features a three-pane design that combines a code editor, a live preview, and a learning pane.

1. **Code Editor Pane**
- Built-in Monaco editor
- Support for various UML diagrams (Use Case, Class, Activity, State, Mind Map, Gantt Chart)

2. **Preview Pane**
- Live preview with instant feedback
- Real-time diagram rendering as you type

3. **Learning Pane**

- Interactive cheat sheets for UML syntax
- Allowing users to understand diagram construction by comparing their work with example solutions.

### Export Options
Diagrams can be exported in these formats:

- PNG format
- SVG format
- TXT format (for source code backup and reuse)

## Project Considerations

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

(2) To prevent resource exhaustion, server storage is managed efficiently by automatically cleaning up temporary files after diagram generation.

## Security Measures


| **Category** | **Description**                                                                                                                                        |
|--------------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| HTML escape  | Applied `htmlspecialchars()` with ENT_QUOTES flag to safely display data from storage by encoding special characters.                                  |
| OS command   | Protected against command injection attacks by using `escapeshellarg()` to properly escape and quote shell arguments when executing PlantUML jar file. |
| Directory    | Restricted public access to `/public` directory only, keeping application logic and configuration files secure. |

