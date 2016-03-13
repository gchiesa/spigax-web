//
// FUNZIONI JAVASCRIPT
//
var jsspigaxpopup = null;

function checkPopup()
{
    if(top.jsspigaxpopup) 
        top.jsspigaxpopup.close();
}

var onLoadArray = new Array();

function onLoadAppend(funcName) 
{
   alert('accodo la funzione |' + funcName + '|');
   onLoadArray[onLoadArray.length] = funcName;
}

function onLoadFunctions()
{
   var c = 0 ;
   
   for(c = 0; c < onLoadArray.length; c++) {
      alert(' lancio la funzione |' + onLoadArray[c] + '|');
      eval(onLoadArray[c]);
   }
   
}

function convertCellToInputText(tableId, cellN, nameText, nameFromCellN, extras)
{
    var k = 0;
    var rows = $(tableId).tBodies[0].rows.length;
    
    for(k = 0; k < rows; k++) {
        
        value = $(tableId).tBodies[0].rows[k].cells[nameFromCellN].innerHTML;
        html = $(tableId).tBodies[0].rows[k].cells[cellN].innerHTML;
        html = html.stripTags().gsub('&euro;', ' ').gsub('€', ' ').strip();
        $(tableId).tBodies[0].rows[k].cells[cellN].innerHTML  = '<input type="text" name="' + nameText + '[' + value + ']" value="' + html + '" ' + extras + ' />';
        
    }
}

function checkDate(object)
{
    var tmpDate = new Date();
    
    var d = object.value.split('/');
    var dd = d[0];
    var mm = d[1];
    var yyyy = d[2];
    
    if(!tmpDate.setFullYear(yyyy)) return object.focus();
    if(!tmpDate.setMonth(mm-1)) return object.focus();
    if(!tmpDate.setDate(dd)) return object.focus();
    
    tmpNum = new Number(tmpDate.getTime());
    if(tmpNum.NaN) return object.focus();
    
    return true;
}
    
function linkToCheckbox(id, type)
{
    if(type == 'PREV') {
        
        id.addClassName('checkbox_link');
        id.previous().click();
        
    } else if(type == 'NEXT') {
        
        id.addClassName('checkbox_link');
        id.next().click();
    }
}

