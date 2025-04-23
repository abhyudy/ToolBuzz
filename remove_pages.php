<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><title>Remove pages</title>
<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<style>
 #preview{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}
 .page{position:relative;border:1px solid #ccc}
 .page canvas{width:170px;height:auto}
 .page input{position:absolute;top:4px;left:4px}
 #viewer{display:none;width:100%;height:550px;border:1px solid #ccc;margin-top:25px}
</style></head>
<body class="container py-5">

<h3 class="text-center mb-4">Remove Pages from PDF</h3>

<form id="frm" enctype="multipart/form-data">
  <input type="file" id="file" name="pdfFile" class="form-control mb-3"
         accept="application/pdf" required>
  <div id="preview"></div>
  <input type="hidden" name="pagesToRemove" id="pages">
  <button id="btn" type="button" class="btn btn-danger btn-block mt-4" disabled>
      Remove selected pages
  </button>
</form>

<h4 class="text-center mt-5">Result</h4>
<iframe id="viewer"></iframe>

<script>
const file   = document.getElementById('file');
const prev   = document.getElementById('preview');
const pages  = document.getElementById('pages');
const btn    = document.getElementById('btn');
const view   = document.getElementById('viewer');
let blobUrl  = null;

/* ----- render thumbnails ----- */
file.addEventListener('change', e=>{
  const f=e.target.files[0]; if(!f) return;
  prev.innerHTML=''; btn.disabled=true;
  const fr=new FileReader();
  fr.onload=()=>pdfjsLib.getDocument(new Uint8Array(fr.result)).promise.then(pdf=>{
     for(let p=1;p<=pdf.numPages;p++){
       pdf.getPage(p).then(pg=>{
         const c=document.createElement('canvas');
         const ctx=c.getContext('2d');
         const vp=pg.getViewport({scale:0.4});
         c.width=vp.width;c.height=vp.height;
         pg.render({canvasContext:ctx,viewport:vp});
         const wrap=document.createElement('div'); wrap.className='page';
         const chk=document.createElement('input'); chk.type='checkbox'; chk.value=p;
         wrap.append(chk,c); prev.append(wrap);
         chk.onchange=()=>{
             pages.value=[...prev.querySelectorAll('input:checked')]
                         .map(x=>x.value).join(',');
             btn.disabled=!pages.value;
         };
       });
     }
  });
  fr.readAsArrayBuffer(f);
});

/* ----- send to server ----- */
btn.onclick=()=>{
  const fd=new FormData(document.getElementById('frm'));
  fetch('process_remove.php',{method:'POST',body:fd})
     .then(r=>r.blob()).then(b=>{
        if(blobUrl) URL.revokeObjectURL(blobUrl);
        blobUrl=URL.createObjectURL(b);
        view.src=blobUrl; view.style.display='block';
     })
     .catch(console.error);
};
</script>
</body></html>
