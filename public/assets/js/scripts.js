document
  .getElementById("feedback-form")
  .addEventListener("submit", function (e) {
    const phone = document.getElementById("phone").value;
    const phonePattern = /^\+?\d{10,15}$/;
    if (!phonePattern.test(phone)) {
      alert("Пожалуйста, введите корректный номер телефона.");
      e.preventDefault();
    }
  });
