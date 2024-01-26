import * as XLSX from 'xlsx';

export const get = function (data = [], file_name = 'export', file_type = 'csv',  columns = [], excluded_fields = ['action']) {
    
    const fields = columns.filter(column => !excluded_fields.includes(column.field));
   
    const options = {
        compression: true,
    }

    if( file_type === 'csv'){
        options.FS = ','
    }

    const details = data.map(item => {
        return fields.reduce((obj, column) => {
            obj[column.field] = item[column.field];
            return obj;  // Return the accumulated object in each iteration
        }, {});
    });

    /* generate worksheet and workbook */
    const worksheet  = XLSX.utils.json_to_sheet(details);
    const workbook   = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, file_name);

    /* fix headers */
    XLSX.utils.sheet_add_aoa(worksheet, [[...fields.map(column => column.name)]], { origin: "A1" });

    /* calculate column width */
    const column_widths = fields.map(column => {
        return {
            wch: 20
        }
    })

    worksheet["!cols"] = column_widths;

    /* create an XLSX file and save in local */
    XLSX.writeFile(workbook,`${file_name}.${file_type}`, options);
}


