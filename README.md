<div align="right">
  <img style="height: 130px; position: absolute; top: 0; right: 0;" src="https://www.htl-ooe.at/wp-content/uploads/2022/08/logo_steyr.png" alt="HTL Steyr Logo">
</div>

# Schulbuchaktion

## Ein Projekt von Schülern der @HTL-Steyr

### Mitwirkende: Felix Gärtner, Samed Karaman, Johannes Kerbl, David Restea

To run the project, make sure the following prerequisites are met:

- PHP is installed
- Docker is installed
- Docker Image generieren zur Datenbankvirtualisierung:
  ```bash
  docker compose up -d
- Run the following command in your terminal in your project directory:
  ```bash
  composer require symfony/runtime
- Starten des Servers (Eingabe in der Windows Power-Shell):
    ```bash
  symfony server:start
- Nun ist es möglich, das Projekt im Browser aufzurufen.
