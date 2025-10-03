

var mode='I';
var upd_row;  
var count=0;

function grid_obj_func()
{
    
    this.Ar1 = new Array();  // add your detail grid elements
    
    this.currentrow=-1;
    
    this.btn_delete;
    this.counter=0;
    this.table_ref;
    
    this.grid_title = function()
    {
        //table_ref = document.getElementById('item_detail_grid');
        //table_ref.insertRow(count);
       
            
        for(i=0;i<this.Ar1.length;i++)
        {
            
            if(this.Ar1[i]['type']!='Hidden' && this.Ar1[i]['type']!='Button')
            {
                  //  alert("kmk"); 
                td  = this.Ar1[i]['header'];
                this.table_ref.rows[count].insertCell();
                this.table_ref.rows[count].cells[this.counter].align = "center";
                this.table_ref.rows[count].cells[this.counter].innerHTML = td;
                this.counter++;
                
                
            }


        }
        alert(this.table_ref.innerHTML);
        count++;

    }
    this.add = function()
    {
        
             
        
        
        if(this.currentrow!=-1)
        {
             
            for(i=0;i<this.Ar1.length;i++)
            {

                if(this.Ar1[i]['type'] == "Combo Box")
                {
                    var cmb = document.getElementById(this.Ar1[i]['name']);
                    this.table_ref.rows[this.currentrow].cells[i].innerHTML ="<input type='text' name='"+this.Ar1[i]['name']+this.currentrow+"' value='"+cmb.options[cmb.selectedIndex].text+"' size='8' style='border: none;'>";    

                }
                else if(this.Ar1[i]['type'] == "Text Box")
                {
                    this.table_ref.rows[this.currentrow].cells[i].innerHTML ="<input type='text' name='"+this.Ar1[i]['name']+this.currentrow+"' value='"+document.getElementById(this.Ar1[i]['name']).value+"' size='8' style='border: none;'>";
                }
                else if(this.Ar1[i]['type'] == "Button")
                {
                    this.table_ref.rows[this.currentrow].cells[i].innerHTML ="<input type='button' name='"+this.Ar1[i]['name']+this.currentrow+"' value='"+this.Ar1[i]['name']+"' onclick=g_obj.update_fun("+document.getElementById(this.currentrow).id+")>";    
                }
                else if(this.Ar1[i]['type'] == "Hidden")
                {
                    this.table_ref.rows[this.currentrow].cells[i].innerHTML ="<input type='hidden' name='"+this.Ar1[i]['name']+this.currentrow+"' value='"+document.getElementById(this.Ar1[i]['name']).value+"'>";    
                }

            }
            
            
        }
        else
        {
             
            this.table_ref.insertRow(count);
            this.table_ref.rows[count].id=count;    
             alert(this.table_ref.innerHTML);
          
            for(i=0;i<this.Ar1.length;i++)
            {
                this.table_ref.rows[count].insertCell();
                this.table_ref.rows[count].cells[i].align = "center";
                
                
                if(this.Ar1[i]['type'] == "Combo Box")
                {
                    var cmb = document.getElementById(this.Ar1[i]['name']);
                    this.table_ref.rows[count].cells[i].innerHTML ="<input type='text' name='"+this.Ar1[i]['name']+count+"' value='"+cmb.options[cmb.selectedIndex].text+"' size='8' style='border: none;'>";    
                                                                            
                }
                if(this.Ar1[i]['type'] == "Text Box")
                {
                    
                    
                    this.table_ref.rows[count].cells[i].innerHTML ="<input type='text' name='"+this.Ar1[i]['name']+count+"' value='"+document.getElementById(this.Ar1[i]['name']).value+"' size='8' style='border: none;' align='middle'>";
                    

                }
                if(this.Ar1[i]['type'] == "Button")
                {
                    
                    
                    this.table_ref.rows[count].cells[i].innerHTML="<input type='button' name='"+this.Ar1[i]['name']+count+"' value='"+this.Ar1[i]['name']+"' onclick=g_obj.update_fun("+document.getElementById(count).id+")>";
                     
                                   
                }
                
                if(this.Ar1[i]['type'] == "Hidden")
                {
                    this.table_ref.rows[count].cells[i].innerHTML ="<input type='hidden' name='"+this.Ar1[i]['name']+count+"' value='"+document.getElementById(this.Ar1[i]['name']).value+"'>";    
                    
                }
                
                 

            }
            
            
            count++;

        }
            
        this.currentrow=-1;
        this.blank_field();
    }
    this.delete_fun = function(flag_id_mode)
    {
       
            if(flag_id_mode == 'I')
            {
                   
                row=document.getElementById(this.currentrow);
                row.parentNode.removeChild(row);
                count--;    
            
            } 
            else if(flag_id_mode == 'U')
            {
                
                this.hide_row(this.currentrow);
            }
            
            this.blank_field(); 
    }
    
    this.update_fun = function(flag_id_mode)
    {
            
            for(i=0;i<this.Ar1.length;i++)
            {
                

                if(this.Ar1[i]['type'] == "Combo Box")
                {
                   var cmb = document.getElementById(this.Ar1[i]['name']);
                   name=this.Ar1[i]['name']+flag_id_mode;
                   
                   
                   for(j=0;j<cmb.options.length;j++)
                   {
                       if(cmb.options[j].text==document.getElementById(name).value)
                       {
                           cmb.selectedIndex = j;
                       }
                   }
                   
                   cmb.disabled="disabled";
                }
                else if(this.Ar1[i]['type'] == "Text Box")
                {
                    
                    name=this.Ar1[i]['name']+flag_id_mode;
                    
                    document.getElementById(this.Ar1[i]['name']).value = document.getElementById(name).value;   
                }
                
                else if(this.Ar1[i]['type'] == "Hidden")
                {
                    
                    name=this.Ar1[i]['name']+flag_id_mode;
                    
                    document.getElementById(this.Ar1[i]['name']).value = document.getElementById(name).value;   
                    
                }
                
                
            }
            this.currentrow = flag_id_mode;
            this.btn_delete.removeAttribute('disabled');
     
    }
    this.blank_field = function()
    {
        
        for(i=0;i<this.Ar1.length;i++)
        {
            if(this.Ar1[i]['type'] == "Combo Box")
            {
                
               var cmb = document.getElementById(this.Ar1[i]['name']); 
               cmb.removeAttribute('disabled');
               cmb.selectedIndex=0;
              
            }
            else if(this.Ar1[i]['type'] == "Button")
            { 
                
                this.btn_delete.setAttribute('disabled','disabled');
            }
            else
            {
                document.getElementById(this.Ar1[i]['name']).value = "";               
            }
        }
        this.currentrow=-1;
        
    }
    this.reset_fun = function()
    {
        
        if(this.currentrow!=-1)
        {
            
            this.update_fun(this.currentrow);
        }
        else
        {
            this.blank_field();
        }
               
    }
    this.hide_row = function(row_id)
    {
        
       document.getElementById(row_id).style.visibility = "hidden";
       document.getElementById(row_id).style.display= "none";
       
    }
    this.add_row = function()
    {
        this.table_ref = document.getElementById('item_detail_grid');
        this.table_ref.insertRow(count);
        this.table_ref.rows[count].id=count;
        return count; 
    }
    this.set_value = function(Ar,row_id,table_ref,col_value)
    {
             
        
        (this.counter > Ar.length-1) ? 1 : table_ref.rows[row_id].insertCell();
        
        if(Ar[this.counter]['type'] == 'Text Box' || Ar[this.counter]['type']  == 'Combo Box')       
        {
            
            table_ref.rows[row_id].cells[this.counter].innerHTML = "<input type='text' name='"+Ar[this.counter]['name']+count+"' value='"+col_value+"' size='8' style='border: none;'>";   
                                         
        }
        else if(Ar[this.counter]['type'] == 'Button')
        {
            
            
            table_ref.rows[row_id].cells[this.counter].innerHTML = "<input type='button' name='"+Ar[this.counter]['name']+count+"' value='"+Ar[this.counter]['name']+"' onclick=g_obj.update_fun("+count+")>";   
                        
        }
        else if(Ar[this.counter]['type'] == 'Hidden')
        {
                    
            table_ref.rows[row_id].cells[this.counter].innerHTML = "<input type='hidden' name='"+Ar[this.counter]['name']+count+"' value='"+col_value+"'>";            
                      
        }
        
        (Ar.length-1==this.counter) ? 1 : this.counter++;
                   
    }

    
        
}


/*function grid_title(title_obj)
{

table_ref = document.getElementById('item_detail_grid');
table_ref.insertRow(count);
alert(title_obj.col.length);

for(i=0;i<title_obj.col.length;i++)
{
td  = title_obj.col[i][3];
table_ref.rows[0].insertCell();
table_ref.rows[0].cells[i].innerHTML = td;
alert(table_ref.innerHTML);

}
count++;
}  */





    


   
    




