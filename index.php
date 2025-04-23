<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ToolsBuzz – PDF Page Remover</title>

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?=BASE_URL?>styles.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>

<style>
 #preview{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}
 .page{position:relative;border:1px solid #ccc}
 .page canvas{width:170px;height:auto}
 .page input{position:absolute;top:4px;left:4px}
 #viewer{display:none;width:100%;height:560px;border:1px solid #ccc;margin-top:30px}
</style>
</head>
<body>

<!-- your navbar markup here (omitted for brevity) -->

<div class="container py-5">
  <h2 class="text-center">PDF Page Remover</h2>

  <form id="frm" enctype="multipart/form-data">
      <input type="file" class="form-control mb-3"
             id="file" name="pdfFile" accept="application/pdf" required>
      <div id="preview"></div>

      <input type="hidden" name="pagesToRemove" id="pages">
      <button id="btn" type="button" class="btn btn-danger btn-block mt-4" disabled>
          Remove selected pages
      </button>
  </form>

  <h4 class="text-center mt-5">Result</h4>
  <iframe id="viewer"></iframe>
</div>

<!-- footer markup here -->

<script>
const file  = document.getElementById('file');
const prev  = document.getElementById('preview');
const pages = document.getElementById('pages');
const btn   = document.getElementById('btn');
const view  = document.getElementById('viewer');
let blobURL = null;

/* render thumbnails */
file.addEventListener('change', () => {
  const f = file.files[0]; if(!f) return;
  prev.innerHTML=''; btn.disabled=true;
  const fr = new FileReader();
  fr.onload = () => pdfjsLib.getDocument(new Uint8Array(fr.result)).promise
    .then(pdf=>{
      for(let p=1;p<=pdf.numPages;p++){
        pdf.getPage(p).then(page=>{
          const c  = document.createElement('canvas');
          const vp = page.getViewport({scale:0.4});
          c.width  = vp.width; c.height = vp.height;
          page.render({canvasContext:c.getContext('2d'),viewport:vp});
          const wrap=document.createElement('div'); wrap.className='page';
          const chk = document.createElement('input'); chk.type='checkbox'; chk.value=p;
          chk.onchange=()=>{
              pages.value=[...prev.querySelectorAll('input:checked')]
                          .map(x=>x.value).join(',');
              btn.disabled=!pages.value;
          };
          wrap.append(chk,c); prev.append(wrap);
        });
      }
    });
  fr.readAsArrayBuffer(f);
});

/* submit to server */
btn.onclick = () =>{
  const fd = new FormData(document.getElementById('frm'));
  fetch('process_remove.php',{method:'POST',body:fd})
    .then(r=>r.blob())
    .then(b=>{
        if(blobURL) URL.revokeObjectURL(blobURL);
        blobURL = URL.createObjectURL(b);
        view.src = blobURL; view.style.display='block';
    })
    .catch(console.error);
};
</script>
</body>
</html>
