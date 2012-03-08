    <br />
    <table border="1">
	<tr>
	    <th>Include</th>
	</tr>
	<tr>
	    <td>
		User Name: {=user.name|strtoupper} <br />
		User Id: {=user.id} <br />
		<hr />
		Real Name: {=user.info.real_name}
		<a href="{/user/=user.id/}">{=user.name}</a>
	    </td>
	</tr>
    </table>
    <br /><br />