const sign_in_btn = document.querySelector("#sign-in-bouton");
const sign_up_btn = document.querySelector("#sign-up-bouton");
const wrapper = document.querySelector(".wrapper");

sign_up_btn.addEventListener("click", () => {
    wrapper.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
    wrapper.classList.remove("sign-up-mode");
});
