<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>نظام الطلبات الاحترافي</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background: #f0f2f5;
        margin: 0;
        padding: 40px 20px;
        direction: rtl;
    }
    .main-container {
        max-width: 800px;
        margin: auto;
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .header-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    h3 { margin: 0; color: #333; }
    .invoice-num-box { font-weight: bold; font-size: 18px; color: #e74c3c; }
    
    .customer-info-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
        text-align: right;
    }
    .info-group {
        display: flex;
        flex-direction: column;
    }
    label { font-weight: bold; margin-bottom: 5px; color: #555; }
    input, textarea, select {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        font-family: 'Arial';
    }

    .add-product-box {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        display: flex;
        gap: 10px;
        align-items: flex-end;
        margin-bottom: 30px;
    }
    .add-product-box div { flex: 1; text-align: right; }
    .btn-add { background: #222; color: #fff; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }

    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; }
    th { background: #f8f9fa; color: #333; }
    
    .btn-edit { background: #ff9800; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    .btn-del { background: #f44336; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    .btn-pdf { background: #2e7d32; color: #fff; padding: 15px; font-size: 18px; border: none; border-radius: 8px; cursor: pointer; flex: 3; font-weight: bold; }
    .btn-reset { background: #7f8c8d; color: #fff; padding: 15px; font-size: 18px; border: none; border-radius: 8px; cursor: pointer; flex: 1; font-weight: bold; }
    
    .footer-date { margin-top: 40px; color: #888; font-size: 14px; text-align: center; }
    .no-print { display: none !important; }
</style>
</head>
<body>

<div class="main-container" id="content-to-export">
    <div class="header-box">
        <h3>👤 معلومات العميل والطلب</h3>
        <div class="invoice-num-box">رقم الفاتورة: #<span id="inv-id">1001</span></div>
    </div>

    <div class="customer-info-section">
        <div class="info-group">
            <label>اسم العميل</label>
            <input type="text" id="cname" placeholder="أدخل الاسم هنا...">
        </div>
        <div class="info-group">
            <label>رقم الهاتف</label>
            <input type="tel" id="cphone" placeholder="07xx xxx xxxx">
        </div>
        <div class="info-group">
            <label>العنوان</label>
            <input type="text" id="address" placeholder="المحافظة / الحي">
        </div>
        <div class="info-group">
            <label>حالة الدفع</label>
            <select id="paymentStatus">
                <option value="كاش (نقد)">كاش (نقد)</option>
                <option value="آجل (لم يتم الدفع)">آجل (دين)</option>

                <option value="واصل (تم الدفع)">واصل</option>
            </select>
        </div>
        <div class="info-group" style="grid-column: span 2;">
            <label>ملاحظات إضافية</label>
            <textarea id="note" rows="2" placeholder="أي ملاحظات أخرى..."></textarea>
        </div>
    </div>

    <div class="add-product-box" id="input-area">
        <div>
            <label>كود المنتج</label>
            <input type="text" id="code">
        </div>
        <div>
            <label>الكمية</label>
            <input type="number" id="qty">
        </div>
        <button type="button" class="btn-add" onclick="addRow()">➕ إضافة للقائمة</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>التسلسل</th>
                <th>كود المنتج</th>
                <th>الكمية</th>
                <th class="action-col">الإجراءات</th>
            </tr>
        </thead>
        <tbody id="rows"></tbody>
    </table>

    <div class="footer-date">
        <p>تاريخ الطلب: <span id="current-date"></span></p>
    </div>
</div>

<div class="action-buttons" style="max-width: 800px; margin: auto;">
    <button type="button" class="btn-pdf" onclick="makePDF()">📄 تحميل الفاتورة (PDF)</button>
    <button type="button" class="btn-reset" onclick="resetForm()">🔄 مسح الكل</button>
</div>

<script>
// تهيئة رقم الفاتورة من الذاكرة المحلية
let currentInvoiceNum = localStorage.getItem('invoiceNum') || 1001;
document.getElementById('inv-id').innerText = currentInvoiceNum;

document.getElementById('current-date').innerText = new Date().toLocaleDateString('ar-EG');

var counter = 1;

function addRow(){
    var code = document.getElementById("code").value.trim();
    var qty = document.getElementById("qty").value.trim();

    if(code === "" || qty === ""){
        alert("يرجى إدخال الكود والكمية");
        return;
    }

    var tr = document.createElement("tr");
    tr.innerHTML = 
        "<td>" + counter + "</td>" +
        "<td>" + code + "</td>" +
        "<td>" + qty + "</td>" +
        "<td class='action-col'>" +
            "<button class='btn-edit' onclick='editRow(this)'>✏️</button> " +
            "<button class='btn-del' onclick='this.parentElement.parentElement.remove()'>✖</button>" +
        "</td>";

    document.getElementById("rows").appendChild(tr);
    counter++;
    document.getElementById("code").value = "";
    document.getElementById("qty").value = "";
}

function editRow(btn){
    var tr = btn.parentElement.parentElement;
    var newCode = prompt("تعديل الكود:", tr.children[1].innerText);
    var newQty = prompt("تعديل الكمية:", tr.children[2].innerText);
    if(newCode) tr.children[1].innerText = newCode;
    if(newQty) tr.children[2].innerText = newQty;
}

function makePDF(){
    var cname = document.getElementById("cname").value.trim();
    if(cname === "") { alert("يرجى إدخال اسم العميل"); return; }

    document.getElementById('input-area').classList.add('no-print');
    document.querySelectorAll('.action-col').forEach(el => el.classList.add('no-print'));

    var element = document.getElementById('content-to-export');
    
    html2pdf().set({
        margin: 0.5,
        filename: 'طلب_رقم_' + currentInvoiceNum + '_' + cname + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).from(element).save().then(() => {
        document.getElementById('input-area').classList.remove('no-print');
        document.querySelectorAll('.action-col').forEach(el => el.classList.remove('no-print'));
        
        // زيادة رقم الفاتورة وحفظه
        currentInvoiceNum++;
        localStorage.setItem('invoiceNum', currentInvoiceNum);
        document.getElementById('inv-id').innerText = currentInvoiceNum;
    });
}




function resetForm() {
    if(confirm("هل أنت متأكد من مسح جميع البيانات؟")) {
        document.getElementById("cname").value = "";
        document.getElementById("cphone").value = "";
        document.getElementById("address").value = "";
        document.getElementById("note").value = "";
        document.getElementById("rows").innerHTML = "";
        counter = 1;
    }
}
</script>

</body>
</html>