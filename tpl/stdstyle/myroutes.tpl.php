
<div id="content">
<table class='border'>
<tr>
<td valign='top'>

<h1>Dodaj Nową trasę <a href='http://wiki.opencaching.pl/My_Route'>Zobacz opis o trasach</a></h1>

<form enctype='multipart/form-data' method='post'>
<table class='border'>
<tr>
<td valign='top' width='25%'>Nazwa trasy:</td>
<td width='70%'><input type='text' name='name' size='50' value=''></td>
</tr>
</table>
<table class='border'>
<tr>
<td valign='top' width='25%'>Opis trasy:</td>
<td width='70%'><textarea name='description' cols='80' rows='3'></textarea></td>
</tr>
</table>
<table class='border'>
<tr>
<td valign='top' width='25%'>Szukaj w promieniu (km):</td>
<td width='70%'><input type='text' name='radius' size='5' value=''></td>
</tr>
</table>
<table class='border'>
<tr>
<td valign='top' width='25%'>Wgraj plik KML :</td>
<td width='70%'><input type='file' name='uploaded' size='50'></td>
</tr>
</table>
<table class='border'>
<tr>
<td width='50%' colspan='2'>Typ skrzynki</td>
<td width='25%'><input type='radio' name='typeix' id='typeix1' value='+' checked><label for='typeix1'> Include cache types</label></td><td width='25%'><input type='radio' name='typeix' id='typeix2' value='-' ><label for='typeix2'> Exclude cache types</label></td></tr>
</tr>
<tr>
<td width='25%'><input type='checkbox' name='type[0]' id='type[0]' value='B' ><label for='type[0]'> Moveable</label></td>
<td width='25%'><input type='checkbox' name='type[5]' id='type[5]' value='M' ><label for='type[5]'> Multi-cache</label></td>
<td width='25%'><input type='checkbox' name='type[6]' id='type[6]' value='O' ><label for='type[6]'> Other</label></td>
<td width='25%'><input type='checkbox' name='type[7]' id='type[7]' value='P' ><label for='type[7]'> Podcache</label></td>
</tr>
<tr>
<td width='25%'><input type='checkbox' name='type[9]' id='type[9]' value='T' ><label for='type[9]'> Traditional</label></td>
<td width='25%'><input type='checkbox' name='type[10]' id='type[10]' value='U' ><label for='type[10]'> Quiz</label></td>
<td width='25%'><input type='checkbox' name='type[11]' id='type[11]' value='V' ><label for='type[11]'> Virtual</label></td>
<td width='25%'><input type='checkbox' name='type[12]' id='type[12]' value='W' ><label for='type[12]'> Webcam</label></td>
</tr>
</table>
<table class='border'>
<tr>
<td width='100%' colspan='4'>Wilkość skrzynki</td>
</tr>
<tr>
<td width='25%'><input type='checkbox' name='container[1]' id='container[1]' value='M' ><label for='container[1]'> Micro</label></td>
<td width='25%'><input type='checkbox' name='container[2]' id='container[2]' value='S' ><label for='container[2]'> Small</label></td>
<td width='25%'><input type='checkbox' name='container[3]' id='container[3]' value='R' ><label for='container[3]'> Regular</label></td>
<td width='25%'><input type='checkbox' name='container[4]' id='container[4]' value='L' ><label for='container[4]'> Large</label></td>
<td width='25%'><input type='checkbox' name='container[6]' id='container[6]' value='O' ><label for='container[6]'> Very large</label></td>
</tr>
</table>
<table class='border'>
<tr>
<td width='25%'>Difficulty range</td>
<td width='25%'>
<select size='1' name='difficulty_start'>
<option selected value='1'>1</option>
<option  value='1.5'>1.5</option>
<option  value='2'>2</option>
<option  value='2.5'>2.5</option>
<option  value='3'>3</option>
<option  value='3.5'>3.5</option>
<option  value='4'>4</option>
<option  value='4.5'>4.5</option>
<option  value='5'>5</option>
</select>
</td>
<td width='25%'>
<select size='1' name='difficulty_finish'>
<option  value='1'>1</option>
<option  value='1.5'>1.5</option>
<option  value='2'>2</option>
<option  value='2.5'>2.5</option>
<option  value='3'>3</option>
<option  value='3.5'>3.5</option>
<option  value='4'>4</option>
<option  value='4.5'>4.5</option>
<option selected value='5'>5</option>
</select>
</td>
<td width='45%'>&nbsp;</td>
</tr>
</table>
<table class='border'>
<tr>
<td width='25%'>Terrain range</td>
<td width='25%'>
<select size='1' name='terrain_start'>
<option selected value='1'>1</option>
<option  value='1.5'>1.5</option>
<option  value='2'>2</option>
<option  value='2.5'>2.5</option>
<option  value='3'>3</option>
<option  value='3.5'>3.5</option>
<option  value='4'>4</option>
<option  value='4.5'>4.5</option>
<option  value='5'>5</option>
</select>
</td>
<td width='25%'>
<select size='1' name='terrain_finish'>
<option  value='1'>1</option>
<option  value='1.5'>1.5</option>
<option  value='2'>2</option>
<option  value='2.5'>2.5</option>
<option  value='3'>3</option>
<option  value='3.5'>3.5</option>
<option  value='4'>4</option>
<option  value='4.5'>4.5</option>
<option selected value='5'>5</option>
</select>
</td>
<td width='45%'>&nbsp;</td>
</tr>
</table>
<table class='border'>
<tr>
<td width='33%'><input type='radio' name='found' id='found1' value='F' onDblClick='this.checked=false' ><label for='found1'> I have found</label></td>
<td width='33%'><input type='radio' name='found' id='found2' value='N' onDblClick='this.checked=false' ><label for='found2'> I haven't found</label></td>
<td width='33%'><span class='smaller2'>Deselect: Double Click Radio Button</span></td>
<tr>
</tr>
<td width='33%'><input type='radio' name='owned' id='owned1' value='O' onDblClick='this.checked=false' ><label for='owned1'> I own</label></td>
<td width='33%'><input type='radio' name='owned' id='owned2' value='N' onDblClick='this.checked=false' ><label for='owned2'> I don't own</label></td>
<td width='33%'>&nbsp;</td>
<tr>
</tr>
<td width='33%'><input type='radio' name='available' id='available1' value='A' onDblClick='this.checked=false' ><label for='available1'> Is Available</label></td>
<td width='33%'><input type='radio' name='available' id='available2' value='U' onDblClick='this.checked=false' ><label for='available2'> Is Unavailable</label></td>
<td width='33%'><input type='radio' name='available' id='available3' value='N' onDblClick='this.checked=false' ><label for='available3'> Is Archived</label></td>
</tr>
</table>
<table>
<tr>
<td width='25%'><input name='button' type='submit' value='Zapisz trasę'></td>
<td width='75%'>&nbsp;</td>
</tr>
</table>
</form>

</td>
</tr>
</table>
</div>
