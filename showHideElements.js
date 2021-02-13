function showMovingElements(id)
{
  document.getElementById("edit_" + id).style.display     = 'none';
  document.getElementById("dropDown_" + id).style.display = 'block';
  document.getElementById("submit_" + id).style.display   = 'block';
  document.getElementById("cancel_" + id).style.display   = 'block';
}

function hideMovingElements(id)
{
  document.getElementById("edit_" + id).style.display     = 'block';
  document.getElementById("dropDown_" + id).style.display = 'none';
  document.getElementById("submit_" + id).style.display   = 'none';
  document.getElementById("cancel_" + id).style.display   = 'none';
}
