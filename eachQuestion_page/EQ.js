window.onload = function () {
  // Populate question details
  questions.forEach((data) => {
    document.getElementById("title_of_question").innerText = data.title;
    document.getElementById("Qdescription").innerText = data.description;
  });

  // Populate answers
  answerss.forEach((data) => {
    generateAnswerCard(data.text, "", data.average_rating, data.AID);
  });

  // Populate question comments
  document.getElementById("question_comments").innerHTML = "";
  questionC.forEach((data) => {
    document.getElementById("question_comments").innerHTML +=
      "-" + data.text + "</br>";
  });
};

// Add event listener for search input
document.getElementById("Search").addEventListener("keyup", function (event) {
  if (event.key === "Enter") {
    searchQuestionsAndAnswers();
  }
});

let rating_name = 0;

function generateAnswerCard(answer, comment, rating, answerId) {
  if (!rating) {
    rating = 0;
  }

  const ansId = document.createElement("input");
  ansId.type = "hidden";
  ansId.name = "answer_iddd";
  ansId.value = answerId;

  rating_name++;
  const answerCard = document.createElement("div");
  answerCard.style.marginTop = "15px";
  answerCard.id = "answer_card";
  answerCard.classList.add("answer-card");
  document.getElementById("EQ_Form").appendChild(answerCard);
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

  const starRatingDiv = document.createElement("div");
  starRatingDiv.className = "star-rating";
  answerCard.appendChild(starRatingDiv);

  for (let i = 5; i >= 1; i--) {
    const input = document.createElement("input");
    input.type = "radio";
    input.name = `rating${answerId}`;
    input.id = `star${i}_${answerId}`;
    input.value = `${i}`;
    input.checked = i === rating;
    starRatingDiv.appendChild(input);

    const label = document.createElement("label");
    label.htmlFor = `star${i}_${answerId}`;
    starRatingDiv.appendChild(label);
  }

  if (comment !== "") {
    const commentHeading = document.createElement("h2");
    commentHeading.style.cssText = "margin-top: -7px; margin-left: 24px";
    commentHeading.textContent = "Comments";
    answerCard.appendChild(commentHeading);

    const commentDescription = document.createElement("p");
    commentDescription.id = "user_comment_on_question";
    commentDescription.textContent = comment;
    answerCard.appendChild(commentDescription);
  }

  const buttons = document.createElement("div");
  buttons.id = "buttons";
  answerCard.appendChild(buttons);

  const deleteImg = document.createElement("img");
  deleteImg.id = "deletbutton";
  deleteImg.src = "../components/pics/delete.png";
  deleteImg.alt = "Delete";
  deleteImg.addEventListener("click", function () {
    postAnswerId(answerId, "delete");
  });
  buttons.appendChild(deleteImg);

  const editImg = document.createElement("img");
  editImg.id = "editbutton";
  editImg.src = "../components/pics/edit.png";
  editImg.alt = "Edit";
  editImg.addEventListener("click", function () {
    const new_answer = window.prompt("What is the new answer?");
    if (new_answer !== null && new_answer !== "") {
      postAnswerId(answerId, "edit", new_answer);
    } else {
      window.alert("Enter a valid answer");
    }
  });
  buttons.appendChild(editImg);

  const submitButton = document.createElement("input");
  submitButton.type = "submit";
  submitButton.value = "Submit";
  submitButton.id = "submitAnswerRDE";
  submitButton.className = "LMSB purpleB";
  submitButton.name = "submitrate";
  buttons.appendChild(submitButton);

  submitButton.addEventListener("click", function (event) {
    event.preventDefault();
    const selectedRating = document.querySelector(
      `input[name="rating${answerId}"]:checked`
    ).value;
    postRating(answerId, selectedRating);
  });
}

function postRating(answerId, rating) {
  const form = document.createElement("form");
  document.body.appendChild(form);
  form.method = "post";
  form.action = "./each_question_page.php"; // Change this to your actual submission path

  const answerIdField = document.createElement("input");
  answerIdField.type = "hidden";
  answerIdField.name = "answer_iddd";
  answerIdField.value = answerId;
  form.appendChild(answerIdField);

  const ratingField = document.createElement("input");
  ratingField.type = "hidden";
  ratingField.name = "rating";
  ratingField.value = rating;
  form.appendChild(ratingField);

  form.submit();
}

function postAnswerId(answerId, action, newAnswer = null) {
  const form = document.createElement("form");
  document.body.appendChild(form);
  form.method = "post";
  form.action = "./each_question_page.php"; // Change this to your actual submission path

  const answerIdField = document.createElement("input");
  answerIdField.type = "hidden";
  answerIdField.name = "answer_iddd";
  answerIdField.value = answerId;
  form.appendChild(answerIdField);

  const actionField = document.createElement("input");
  actionField.type = "hidden";
  actionField.name = action === "delete" ? "delete_answer" : "edit_answer";
  form.appendChild(actionField);

  if (action === "edit" && newAnswer !== null) {
    const newAnswerField = document.createElement("input");
    newAnswerField.type = "hidden";
    newAnswerField.name = "edited_answer";
    newAnswerField.value = newAnswer;
    form.appendChild(newAnswerField);
  }

  form.submit();
}

function searchQuestionsAndAnswers() {
  const searchValue = document.getElementById("Search").value;
  const form = document.createElement("form");
  document.body.appendChild(form);
  form.method = "post";
  form.action = "./each_question_page.php"; // Change this to your actual submission path

  const searchField = document.createElement("input");
  searchField.type = "hidden";
  searchField.name = "Searched_Value";
  searchField.value = searchValue;
  form.appendChild(searchField);

  form.submit();
}

document.getElementById("edit_question").addEventListener("click", () => {
  const question_title_value = window.prompt("What is the new title:");
  const question_description_value = window.prompt(
    "What is the new description:"
  );
  if (question_title_value || question_description_value) {
    document.getElementById("Q_title").value = question_title_value;
    document.getElementById("Q_description").value = question_description_value;
    document.getElementById("hiddenSub").click();
  } else {
    window.alert("You must edit either title or description");
  }
});

document.getElementById("delete_question").addEventListener("click", () => {
  document.getElementById("hiddenDeleteQ").click();
});

// 22222222
//window.onload = function () {
//   // Populate question details
//   questions.forEach((data) => {
//     document.getElementById("title_of_question").innerText = data.title;
//     document.getElementById("Qdescription").innerText = data.description;
//   });

//   // Populate answers
//   answerss.forEach((data) => {
//     generateAnswerCard(data.text, "", data.average_rating, data.AID);
//   });

//   // Populate question comments
//   document.getElementById("question_comments").innerHTML = "";
//   questionC.forEach((data) => {
//     document.getElementById("question_comments").innerHTML +=
//       "-" + data.text + "</br>";
//   });
// };

// let rating_name = 0;

// function generateAnswerCard(answer, comment, rating, answerId) {
//   if (!rating) {
//     rating = 0;
//   }

//   const ansId = document.createElement("input");
//   ansId.type = "hidden";
//   ansId.name = "answer_iddd";
//   ansId.value = answerId;

//   rating_name++;
//   const answerCard = document.createElement("div");
//   answerCard.style.marginTop = "15px";
//   answerCard.id = "answer_card";
//   answerCard.classList.add("answer-card");
//   document.getElementById("EQ_Form").appendChild(answerCard);
//   answerCard.appendChild(ansId);

//   const answerHeading = document.createElement("h2");
//   answerHeading.style.cssText = "margin-left: 24px; margin-top: 15px";
//   answerHeading.textContent = "Answer";
//   answerCard.appendChild(answerHeading);

//   const userDescription = document.createElement("p");
//   userDescription.id = "user_description";
//   userDescription.textContent = answer;
//   answerCard.appendChild(userDescription);

//   const rateDiv = document.createElement("div");
//   rateDiv.id = "Rate";
//   answerCard.appendChild(rateDiv);
//   const starIcon = document.createElement("img");
//   starIcon.src =
//     "data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'%3E%3Cpath d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/%3E%3C/svg%3E";
//   starIcon.alt = "Star rating";
//   starIcon.style.cssText = "height: 13px; margin-left: 5px";
//   rateDiv.appendChild(starIcon);
//   const rateText = document.createTextNode(rating);
//   rateDiv.appendChild(rateText);

//   // Create star ratings
//   const starRatingDiv = document.createElement("div");
//   starRatingDiv.className = "star-rating";
//   answerCard.appendChild(starRatingDiv);

//   for (let i = 5; i >= 1; i--) {
//     const input = document.createElement("input");
//     input.type = "radio";
//     input.name = `rating${answerId}`;
//     input.id = `star${i}_${answerId}`;
//     input.value = `${i}`;
//     input.checked = i === rating; // Set the correct rating as checked
//     input.disabled = false; // Make stars read-only
//     starRatingDiv.appendChild(input);

//     const label = document.createElement("label");
//     label.htmlFor = `star${i}_${answerId}`;
//     starRatingDiv.appendChild(label);
//   }

//   if (comment !== "") {
//     const commentHeading = document.createElement("h2");
//     commentHeading.style.cssText = "margin-top: -7px; margin-left: 24px";
//     commentHeading.textContent = "Comments";
//     answerCard.appendChild(commentHeading);

//     const commentDescription = document.createElement("p");
//     commentDescription.id = "user_comment_on_question";
//     commentDescription.textContent = comment;
//     answerCard.appendChild(commentDescription);
//   }

//   const buttons = document.createElement("div");
//   buttons.id = "buttons";
//   answerCard.appendChild(buttons);

//   const deleteImg = document.createElement("img");
//   deleteImg.id = "deletbutton";
//   deleteImg.src = "../components/pics/delete.png";
//   deleteImg.alt = "Delete";
//   deleteImg.addEventListener("click", function () {
//     postAnswerId(answerId, "delete");
//   });
//   buttons.appendChild(deleteImg);

//   const editImg = document.createElement("img");
//   editImg.id = "editbutton";
//   editImg.src = "../components/pics/edit.png";
//   editImg.alt = "Edit";
//   editImg.addEventListener("click", function () {
//     const new_answer = window.prompt("What is the new answer?");
//     if (new_answer !== null && new_answer !== "") {
//       postAnswerId(answerId, "edit", new_answer);
//     } else {
//       window.alert("Enter a valid answer");
//     }
//   });
//   buttons.appendChild(editImg);

//   // Submit Button
//   const submitButton = document.createElement("input");
//   submitButton.type = "submit";
//   submitButton.value = "Submit";
//   submitButton.id = "submitAnswerRDE";
//   submitButton.className = "LMSB purpleB";
//   submitButton.name = "submitrate";
//   buttons.appendChild(submitButton);
// }

// function postAnswerId(answerId, action, newAnswer = null) {
//   const form = document.createElement("form");
//   document.body.appendChild(form);
//   form.method = "post";
//   form.action = "./each_question_page.php"; // Change this to your actual submission path

//   const answerIdField = document.createElement("input");
//   answerIdField.type = "hidden";
//   answerIdField.name = "answer_iddd";
//   answerIdField.value = answerId;
//   form.appendChild(answerIdField);

//   const actionField = document.createElement("input");
//   actionField.type = "hidden";
//   actionField.name = action === "delete" ? "delete_answer" : "edit_answer";
//   form.appendChild(actionField);

//   if (action === "edit" && newAnswer !== null) {
//     const newAnswerField = document.createElement("input");
//     newAnswerField.type = "hidden";
//     newAnswerField.name = "edited_answer";
//     newAnswerField.value = newAnswer;
//     form.appendChild(newAnswerField);
//   }

//   form.submit(); // Submit the form with the answer ID and action
// }

// document.getElementById("edit_question").addEventListener("click", () => {
//   const question_title_value = window.prompt("What is the new title:");
//   const question_description_value = window.prompt(
//     "What is the new description:"
//   );
//   if (question_title_value || question_description_value) {
//     document.getElementById("Q_title").value = question_title_value;
//     document.getElementById("Q_description").value = question_description_value;
//     document.getElementById("hiddenSub").click();
//   } else {
//     window.alert("You must edit either title or description");
//   }
// });

// document.getElementById("delete_question").addEventListener("click", () => {
//   document.getElementById("hiddenDeleteQ").click();
// });

// window.onload = function () {
//   questions.forEach((data) => {
//     document.getElementById("title_of_question").innerText = data.title;
//     document.getElementById("Qdescription").innerText = data.description;
//   });
//   answerss.forEach((data) => {
//     generateAnswerCard(data.text, "", data.average_rating, data.AID);
//   });
//   document.getElementById("question_comments").innerHTML = "";
//   questionC.forEach((data) => {
//     document.getElementById("question_comments").innerHTML +=
//       "-" + data.text + "</br>";
//   });
// };

// let rating_name = 0;

// function generateAnswerCard(answer, comment, rating, answerId) {
//   if (!rating) {
//     rating = 0;
//   }

//   const ansId = document.createElement("input");
//   ansId.type = "hidden";
//   ansId.name = "answer_iddd";
//   ansId.value = answerId;

//   rating_name++;
//   const answerCard = document.createElement("div");
//   answerCard.classList.add("answer-card");
//   answerCard.id = "answer_card";
//   answerCard.style.marginTop = "15px";
//   document.getElementById("EQ_Form").appendChild(answerCard);
//   answerCard.appendChild(ansId);

//   const answerHeading = document.createElement("h2");
//   answerHeading.style.cssText = "margin-left: 24px; margin-top: 15px";
//   answerHeading.textContent = "Answer";
//   answerCard.appendChild(answerHeading);

//   const userDescription = document.createElement("p");
//   userDescription.id = "user_description";
//   userDescription.textContent = answer;
//   answerCard.appendChild(userDescription);

//   const rateDiv = document.createElement("div");
//   rateDiv.id = "Rate";
//   answerCard.appendChild(rateDiv);

//   const rateText = document.createTextNode(rating);
//   rateDiv.appendChild(rateText);

//   const starIcon = document.createElement("img");
//   starIcon.src =
//     "data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'%3E%3Cpath d='M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z'/%3E%3C/svg%3E";
//   starIcon.alt = "Star rating";
//   starIcon.style.cssText = "height: 13px; margin-left: 5px";
//   rateDiv.appendChild(starIcon);

//   if (comment !== "") {
//     const commentHeading = document.createElement("h2");
//     commentHeading.style.cssText = "margin-top: -7px; margin-left: 24px";
//     commentHeading.textContent = "Comments";
//     answerCard.appendChild(commentHeading);

//     const commentDescription = document.createElement("p");
//     commentDescription.id = "user_comment_on_question";
//     commentDescription.textContent = comment;
//     answerCard.appendChild(commentDescription);
//   }

//   const buttons = document.createElement("div");
//   buttons.classList.add("buttons");
//   answerCard.appendChild(buttons);

//   const deleteImg = document.createElement("img");
//   deleteImg.classList.add("deletebutton");
//   deleteImg.src = "../components/pics/delete.png";
//   deleteImg.style.cssText = "position: absolute; height: 25px; top: 240px;";
//   deleteImg.alt = "Delete";
//   buttons.appendChild(deleteImg);

//   const editImg = document.createElement("img");
//   editImg.classList.add("editbutton");
//   editImg.src = "../components/pics/edit.png";
//   editImg.style.cssText =
//     "position: absolute; height: 25px; top: 240px; left: 50px;";
//   editImg.alt = "Edit";
//   buttons.appendChild(editImg);

//   const hidden_delete = document.createElement("input");
//   hidden_delete.name = "delete_answer";
//   hidden_delete.value = "delete answer";
//   hidden_delete.type = "hidden";
//   hidden_delete.classList.add("hidden_delete");
//   buttons.appendChild(hidden_delete);

//   const hidden_edit = document.createElement("input");
//   hidden_edit.name = "edit_answer";
//   hidden_edit.value = "edit answer";
//   hidden_edit.type = "hidden";
//   hidden_edit.classList.add("hidden_edit");
//   buttons.appendChild(hidden_edit);

//   const starRatingDiv = document.createElement("div");
//   starRatingDiv.className = "star-rating";
//   buttons.appendChild(starRatingDiv);

//   const edited_answer = document.createElement("input");
//   edited_answer.type = "hidden";
//   edited_answer.name = "edited_answer";
//   edited_answer.classList.add("edited_answer");
//   buttons.appendChild(edited_answer);

//   addEventListenersToButtons(deleteImg, editImg, answerId);
// }

// function addEventListenersToButtons(deleteButton, editButton, answerId) {
//   deleteButton.addEventListener("click", function () {
//     postAnswerId(answerId, "delete");
//   });

//   editButton.addEventListener("click", function () {
//     const choice = window.prompt(
//       "Do you want to edit the answer or add a comment?\n" +
//         "0 for edit\n" +
//         "1 for add comment"
//     );
//     if (choice == 0) {
//       const new_answer = window.prompt("What is the new answer?");
//       if (new_answer === "") {
//         window.alert("Enter a valid answer");
//       } else {
//         postAnswerId(answerId, "edit", new_answer);
//       }
//     } else if (choice == 1) {
//       const comment_to_answer = window.prompt("Enter your comment");
//       if (comment_to_answer !== "") {
//         const commentHeading = document.createElement("h2");
//         commentHeading.style.cssText = "margin-top: -7px; margin-left: 24px";
//         commentHeading.textContent = "Comments";
//         const commentDescription = document.createElement("p");
//         commentDescription.id = "user_comment_on_question";
//         commentDescription.textContent = comment_to_answer;
//         const answerCard = editButton.closest(".answer-card");
//         answerCard.appendChild(commentHeading);
//         answerCard.appendChild(commentDescription);
//       }
//     } else {
//       window.alert("Enter a valid number");
//     }
//   });
// }

// function postAnswerId(answerId, action, newText = null) {
//   const form = document.createElement("form");
//   document.body.appendChild(form);
//   form.method = "post";
//   form.action = "./each_question_page.php";

//   const hiddenIdField = document.createElement("input");
//   hiddenIdField.type = "hidden";
//   hiddenIdField.name = "answer_iddd";
//   hiddenIdField.value = answerId;
//   form.appendChild(hiddenIdField);

//   if (action === "edit") {
//     const hiddenTextField = document.createElement("input");
//     hiddenTextField.type = "hidden";
//     hiddenTextField.name = "edited_answer";
//     hiddenTextField.value = newText;
//     form.appendChild(hiddenTextField);

//     const hiddenEditField = document.createElement("input");
//     hiddenEditField.type = "hidden";
//     hiddenEditField.name = "edit_answer";
//     hiddenEditField.value = "edit answer";
//     form.appendChild(hiddenEditField);
//   } else if (action === "delete") {
//     const hiddenDeleteField = document.createElement("input");
//     hiddenDeleteField.type = "hidden";
//     hiddenDeleteField.name = "delete_answer";
//     hiddenDeleteField.value = "delete answer";
//     form.appendChild(hiddenDeleteField);
//   }

//   form.submit();
// }

// document.getElementById("edit_question").addEventListener("click", () => {
//   const question_title_value = window.prompt("What is the new title:");
//   const question_description_value = window.prompt(
//     "What is the new description:"
//   );
//   if (question_title_value || question_description_value) {
//     document.getElementById("Q_title").value = question_title_value;
//     document.getElementById("Q_description").value = question_description_value;
//     document.getElementById("hiddenSub").click();
//   } else {
//     window.alert("You must edit either title or description");
//   }
// });

// document.getElementById("delete_question").addEventListener("click", () => {
//   document.getElementById("hiddenDeleteQ").click();
// });
