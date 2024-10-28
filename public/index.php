<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Форма обратной связи</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <h1>Обратная связь</h1>
    <form action="submit.php" method="POST" id="feedback-form">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required>

        <label for="last_name">Фамилия:</label>
        <input type="text" id="last_name" name="last_name">

        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" pattern="\+?\d{10,15}" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="type">Тип запроса:</label>
        <select id="type" name="type" required>
            <option value="">Выберите тип</option>
            <option value="Консультация">Консультация</option>
            <option value="Поддержка">Поддержка</option>
            <option value="Обратная связь">Обратная связь</option>
        </select>


        <label for="message">Сообщение:</label>
        <textarea id="message" name="message" rows="5" required></textarea>

        <button type="submit">Отправить</button>
    </form>

    <script src="assets/js/scripts.js"></script>
</body>

</html>