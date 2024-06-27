// exportExcel.js
import FileSaver from "file-saver";
import XLSX from "xlsx";

export function exportExcel(data,title) {
    // const data = excelData.map((item) => ({
    //   id: item.id,
    //   "费用周期": item.period_id_driver,
    //   "费用类别": item.type_name,
    //   "其他类别": item.other_type,
    //   "费用金额": item.cost_money,
    //   "驾驶员": item.driver_name,
    //   "挂车号": item.trailer_num,
    //   "费用照片": item.cost_img,
    //   "备注": item.remark,
    //   "添加人": item.cost_creater
    // }));
    console.log(title)
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.json_to_sheet(data);
  
    // 添加图片
    data.forEach((item, index) => {
      const img = new Image();
      img.src = item.cost_img; // Assuming cost_img is the image source
      const ctx = document.createElement('canvas').getContext('2d');
      img.onload = () => {
        ctx.canvas.width = img.width;
        ctx.canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        const dataURI = ctx.canvas.toDataURL('image/jpeg');
        XLSX.utils.sheet_addImage(ws, `A${index + 2}`, dataURI, { extension: 'png' });
      };
    });
  
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
  
    const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
    try {
      FileSaver.saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        title+'.xlsx'
      );
    } catch (e) {
      console.log(e);
    }
  }
  
  