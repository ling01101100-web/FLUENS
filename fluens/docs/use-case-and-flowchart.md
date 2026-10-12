# Pomodoro To-Do: Use Cases, Flowcharts and Database Diagram

Paste each block into https://mermaid.live (or any Mermaid viewer) to export a PNG/PDF for your report.

## 1. Use case diagram (roles and what each can do)

```mermaid
flowchart LR
  Guest([Guest]):::actor
  User([User]):::actor
  Manager([Manager]):::actor
  Admin([Admin]):::actor

  subgraph System[Pomodoro To-Do]
    UC1((Register))
    UC2((Log in / Log out))
    UC3((Manage own tasks))
    UC4((Manage own categories))
    UC5((Run Pomodoro timer))
    UC6((View own dashboard))
    UC7((View team report))
    UC8((Manage users and roles))
  end

  Guest --> UC1
  Guest --> UC2
  User --> UC2 & UC3 & UC4 & UC5 & UC6
  Manager -. "inherits User" .-> User
  Manager --> UC7
  Admin -. "inherits Manager" .-> Manager
  Admin --> UC8
  classDef actor fill:#fff,stroke:#1b2a2f;
```

## 2. Flowchart: request routing and role check

```mermaid
flowchart TD
  A([Browser requests index.php?page=X]) --> B{Route X exists?}
  B -- No --> E404[Show 404 page]
  B -- Yes --> C{Route needs login?}
  C -- "No (guest page)" --> D{Already logged in?}
  D -- Yes --> R1[Redirect to dashboard]
  D -- No --> P[Load page]
  C -- Yes --> F{Logged in?}
  F -- No --> R2[Flash message, redirect to login]
  F -- Yes --> G{Role allowed for X?}
  G -- No --> E403[Show 403 page]
  G -- Yes --> P
  P --> H([Render page inside layout])
```

## 3. Flowchart: login

```mermaid
flowchart TD
  A([Open login page]) --> B[/Enter username and password/]
  B --> C{CSRF token valid?}
  C -- No --> X[Show session expired message]
  C -- Yes --> D[Find user and role in database]
  D --> E{User found and password_verify OK?}
  E -- No --> F[Show error, stay on login]
  E -- Yes --> G[Regenerate session ID, store id, username, role]
  G --> H([Redirect to dashboard])
```

## 4. Flowchart: one Pomodoro session (data flow)

```mermaid
flowchart TD
  A([Open Timer page]) --> B[Pick a task, or none]
  B --> C[Press Start: JavaScript counts down]
  C --> D{Time reached 00:00?}
  D -- No --> C
  D -- Yes --> E[Browser POSTs type, task_id, CSRF token to log_session]
  E --> F{Valid type and task owned by user?}
  F -- No --> G[Return error JSON]
  F -- Yes --> H[(INSERT into pomodoro_sessions)]
  H --> I[Return today's count]
  I --> J{Was it a focus session?}
  J -- Yes --> K{Count divisible by 4?}
  K -- Yes --> L[Switch to long break]
  K -- No --> M[Switch to short break]
  J -- No --> N[Switch to focus]
```

## 5. Entity relationship diagram

```mermaid
erDiagram
  roles ||--o{ users : "has (role_id)"
  users ||--o{ categories : "owns (user_id)"
  users ||--o{ tasks : "owns (user_id)"
  categories ||--o{ tasks : "groups (category_id)"
  users ||--o{ pomodoro_sessions : "completes (user_id)"
  tasks ||--o{ pomodoro_sessions : "tracked by (task_id)"

  roles { tinyint id PK
          varchar name }
  users { int id PK
          tinyint role_id FK
          varchar username
          varchar email
          varchar password_hash }
  categories { int id PK
               int user_id FK
               varchar name }
  tasks { int id PK
          int user_id FK
          int category_id FK
          varchar title
          tinyint est_pomodoros
          enum status }
  pomodoro_sessions { int id PK
                      int user_id FK
                      int task_id FK
                      enum type
                      tinyint duration_minutes }
```
