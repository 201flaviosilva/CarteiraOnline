$(document).ready(function () {
  $("#TabelaContas").DataTable();
});

const d = new Date();
document.getElementById("DataFinal").value = `${String(
  new Date().toISOString().slice(0, 10)
)}`;
