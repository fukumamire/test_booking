document.addEventListener("DOMContentLoaded", function () {
  const searchForm = document.getElementById("search__form");
  searchForm.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      searchForm.submit();
    }
  });

  // 画像の読み込みとDOMへの追加
  const searchIcon = new Image();
  searchIcon.src = window.searchIconPath; // グローバル変数を使用
  searchIcon.onload = function () {
    const existingIcon = document.getElementById('searchIcon'); // 既存のアイコンをIDで選択
    if (existingIcon) {
      existingIcon.src = this.src; // 既存のアイコンのsrcを新しく読み込んだ画像のsrcに置き換え
    } else {
    }
  };
  searchIcon.onerror = function () {
    console.error('Failed to load the search icon image.');
  };
});
