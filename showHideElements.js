function showMovingElements(id)
{
  var id_edit = "edit_" + id;
  var id_dropDown = "dropDown_" + id;
  var id_submit = "submit_" + id;
  var id_cancel = "cancel_" + id;

  document.getElementById(id_edit).style.display = 'none';
  document.getElementById(id_dropDown).style.display = 'block';
  document.getElementById(id_submit).style.display = 'block';
  document.getElementById(id_cancel).style.display = 'block';
}

function hideMovingElements(id)
{
  var id_edit = "edit_" + id;
  var id_dropDown = "dropDown_" + id;
  var id_submit = "submit_" + id;
  var id_cancel = "cancel_" + id;

  document.getElementById(id_edit).style.display = 'block';
  document.getElementById(id_dropDown).style.display = 'none';
  document.getElementById(id_submit).style.display = 'none';
  document.getElementById(id_cancel).style.display = 'none';
}
