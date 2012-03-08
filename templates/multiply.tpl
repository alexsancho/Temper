<ko:import file="imported" />
    <table border="1">
	<tr>
	    <th colspan="10">If Else ElseIf</th>
	</tr>
	<tr>
	    <td>
	    <ko:if var="a" eq="a">a<ko:elseif var="a" eq="b" />b</ko:if>
	    <hr />
	    <ko:if var="{%user.id}" neq="">set<ko:else />not set</ko:if>
	    </td>
	</tr>
    </table>
    <table border="1">
	<tr>
	    <th colspan="10">For</th>
	</tr>
	<ko:for var="x" start="1" end="10" increment="1">
	<tr>
	    <ko:for var="y" start="1" end="10" increment="1">
	    <td><ko:print>$x * $y</ko:print></td>
	    </ko:for>
	</tr>
	</ko:for>
    </table><br />
    <table border="1">
	<tr>
	    <th>Switch</th>
	</tr>
	<ko:switch var="a">
	<ko:case val="a">
	<tr>
	    <td>a</td>
	</tr>
	</ko:case>
	<ko:case val="b">
	<tr>
	    <td>b</td>
	</tr>
	</ko:case>
	</ko:switch>
    </table>
    <table border="1">
	<tr>
	    <th colspan="2">Foreach</th>
	</tr>
	<tr>
	    <th>Keys</th>
	    <th>Values</th>
	</tr>
	<ko:foreach var="array" key="key" val="val">
	<tr>
	    <td>{=key}</td>
	    <td>{=val}</td>
	</tr>	
	</ko:foreach>
    </table>
    
    {{Kohana::debug({%a})}}
    
    
    <?php echo 'this should be removed'?>
    <?='this too'?>

    
    