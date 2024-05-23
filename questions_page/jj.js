window.onload = function () {
  if (!isAnswer) {
    question_cardsss.forEach((data) => {
      add_card(data.title, data.description, data.TotalAnswers, data.QID);
    });
  } else {
    answer_cards.forEach((data) => {
      generateMyAnswerCard(data.answer_text, "", data.average_rating, data.AID);
    });
  }
};
function generateMyAnswerCard(answer, comment, rating, answerId) {
  if (!rating) {
    rating = 0;
  }
  if (!comment) {
    comment = "";
  }
  rating_name++;

  const answerCard = document.createElement("div");
  answerCard.classList.add("answer-card");
  answerCard.id = "answer_card";
  answerCard.style.marginTop = "15px";
  answerCard.dataset.answerId = answerId;
  document.getElementById("my_answers_form").appendChild(answerCard);

  const ansId = document.createElement("input");
  ansId.type = "hidden";
  ansId.name = "answer_iddd";
  ansId.value = answerId;
  answerCard.appendChild(ansId);

  const answerHeading = document.createElement("h2");
  answerHeading.style.cssText = "margin-left: 24px; margin-top: 15px";
  answerHeading.textContent = "Answer";
  answerCard.appendChild(answerHeading);

  const userDescription = document.createElement("p");
  userDescription.id = "user_description";
  userDescription.textContent = answer;
  answerCard.appendChild(userDescription);

  const rateDiv = document.createElement("div");
  rateDiv.id = "Rate";
  answerCard.appendChild(rateDiv);

  const rateText = document.createTextNode(rating);
  rateDiv.appendChild(rateText);

  const starIcon = document.createElement("img");
  starIcon.src =
    "data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'%3E%3Cpath d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/%3E%3C/svg%3E";
  starIcon.alt = "Star rating";
  starIcon.style.cssText = "height: 13px; margin-left: 5px";
  rateDiv.appendChild(starIcon);

  if (comment !== "") {
    const commentHeading = document.createElement("h2");
    commentHeading.style.cssText = "margin-top: -7px; margin-left: 24px";
    commentHeading.textContent = "Comments";
    answerCard.appendChild(commentHeading);

    // Create Comment Description
    const commentDescription = document.createElement("p");
    commentDescription.id = "user_comment_on_question";
    commentDescription.textContent = comment;
    answerCard.appendChild(commentDescription);
  }

  const buttons = document.createElement("div");
  buttons.classList.add("buttons");
  answerCard.appendChild(buttons);

  const deleteImg = document.createElement("img");
  deleteImg.id = "deletebutton";
  deleteImg.src = "../components/pics/delete.png";
  deleteImg.style.cssText = "position: absolute; height: 25px; top: 240px;";
  deleteImg.alt = "Delete";
  buttons.appendChild(deleteImg);

  const editImg = document.createElement("img");
  editImg.id = "editbuttonn";
  editImg.src = "../components/pics/edit.png";
  editImg.alt = "Edit";
  buttons.appendChild(editImg);

  const hidden_delete = document.createElement("input");
  hidden_delete.name = "delete_answer";
  hidden_delete.value = "delete answer";
  hidden_delete.type = "hidden";
  hidden_delete.classList.add("hidden_delete");
  buttons.appendChild(hidden_delete);

  const hidden_edit = document.createElement("input");
  hidden_edit.name = "edit_answer";
  hidden_edit.value = "edit answer";
  hidden_edit.type = "hidden";
  hidden_edit.classList.add("hidden_edit");
  buttons.appendChild(hidden_edit);

  const starRatingDiv = document.createElement("div");
  starRatingDiv.className = "star-rating";
  buttons.appendChild(starRatingDiv);

  const edited_answer = document.createElement("input");
  edited_answer.type = "hidden";
  edited_answer.name = "edited_answer";
  edited_answer.classList.add("edited_answer");
  buttons.appendChild(edited_answer);

  addEventListenersToButtons(deleteImg, editImg, answerId);
}

function addEventListenersToButtons(deleteButton, editButton, answerId) {
  deleteButton.addEventListener("click", function () {
    postAnswerId(answerId, "delete");
  });

  editButton.addEventListener("click", function () {
    const choice = window.prompt(
      "Do you want to edit the answer or add a comment?\n" +
        "0 for edit\n" +
        "1 for add comment"
    );
    if (choice == 0) {
      const new_answer = window.prompt("What is the new answer?");
      if (new_answer === "") {
        window.alert("Enter a valid answer");
      } else {
        postAnswerId(answerId, "edit", new_answer);
      }
    } else if (choice == 1) {
      const comment_to_answer = window.prompt("Enter your comment");
      if (comment_to_answer !== "") {
        const commentHeading = document.createElement("h2");
        commentHeading.style.cssText = "margin-top: -7px; margin-left: 24px";
        commentHeading.textContent = "Comments";
        const commentDescription = document.createElement("p");
        commentDescription.id = "user_comment_on_question";
        commentDescription.textContent = comment_to_answer;
        const answerCard = editButton.closest(".answer-card");
        answerCard.appendChild(commentHeading);
        answerCard.appendChild(commentDescription);
      }
    } else {
      window.alert("Enter a valid number");
    }
  });
}

function postAnswerId(answerId, action, newText = null) {
  const form = document.createElement("form");
  document.body.appendChild(form);
  form.method = "post";
  form.action = "./questions.php";

  const hiddenIdField = document.createElement("input");
  hiddenIdField.type = "hidden";
  hiddenIdField.name = "answer_iddd";
  hiddenIdField.value = answerId;
  form.appendChild(hiddenIdField);

  if (action === "edit") {
    const hiddenTextField = document.createElement("input");
    hiddenTextField.type = "hidden";
    hiddenTextField.name = "edited_answer";
    hiddenTextField.value = newText;
    form.appendChild(hiddenTextField);

    const hiddenEditField = document.createElement("input");
    hiddenEditField.type = "hidden";
    hiddenEditField.name = "edit_answer";
    hiddenEditField.value = "edit answer";
    form.appendChild(hiddenEditField);
  } else if (action === "delete") {
    const hiddenDeleteField = document.createElement("input");
    hiddenDeleteField.type = "hidden";
    hiddenDeleteField.name = "delete_answer";
    hiddenDeleteField.value = "delete answer";
    form.appendChild(hiddenDeleteField);
  }

  form.submit();
}

let numOfQuestions = 0;
function add_card(Qtitle, Qdescription, NOAnswers, Qid) {
  if (!NOAnswers) {
    NOAnswers = 0;
  }
  numOfQuestions++;
  const card = document.createElement("div");
  const first_Section = document.createElement("div");
  const answers = document.createElement("div");
  const Question_title = document.createElement("div");
  const Question_Discription = document.createElement("div");

  // Using class instead of id
  card.id = "QCard";
  first_Section.id = "first_Section";
  answers.id = "answers";
  Question_title.id = "Question_title";
  Question_Discription.id = "Question_Discription";

  // setting content
  answers.textContent = `${NOAnswers} answers`;
  Question_title.style.cssText = "margin-left: 90px; margin-top: 5px;";
  Question_title.textContent = Qtitle;
  Question_Discription.textContent = Qdescription;

  // appending elements
  document.getElementById("questionsF").appendChild(card);
  const hiddenInput = document.createElement("input");
  hiddenInput.type = "hidden";
  hiddenInput.setAttribute("name", "Qid");
  hiddenInput.value = Qid; // Set value directly

  card.appendChild(first_Section);
  card.appendChild(hiddenInput); // append hidden input directly to card
  first_Section.appendChild(answers);
  card.appendChild(Question_title);
  card.appendChild(Question_Discription);

  card.style.cursor = "pointer";
  card.addEventListener("click", function () {
    postQuestionId(Qid);
  });
}

function postQuestionId(Qid) {
  const form = document.createElement("form");
  document.body.appendChild(form);
  form.method = "post";
  form.action = "../eachQuestion_page/each_question_page.php"; // Change this to your actual submission path

  const hiddenField = document.createElement("input");
  hiddenField.type = "hidden";
  hiddenField.name = "Qid";
  hiddenField.value = Qid;
  form.appendChild(hiddenField);

  form.submit(); // Submit the form with the question ID
}

let rating_name = 0;
