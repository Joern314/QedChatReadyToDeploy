var last;

function Init ()
{
	if (parent != self)
		parent.InitHelp ();
	else
	{
		var options = new Object ();
		options["ip"] = 1;
		options["delay"] = 0;
		options["links"] = 1;
		options["old"] = 0;
		options["last"] = 20;
		options["target"] = "_blank";
		InitRemote (options);
	}
}

function InitRemote (options)
{
	document.getElementById ("ip").checked = options["ip"];
	document.getElementById ("delay").checked = options["delay"];
	document.getElementById ("links").checked = options["links"];
	document.getElementById ("old").checked = options["old"];
	document.getElementById ("last").value = count = options["last"];
	
	document.getElementById ("info").target = options["target"];
	//document.getElementById ("links").target = options["target"];
}

function ShowIp ()
{
	if (parent != self)
		parent.ShowIp (document.getElementById ("ip").checked);	
}

function ShowDelay ()
{
	if (parent != self)
		parent.ShowDelay (document.getElementById ("delay").checked);
}

function ShowLinks ()
{
	if (parent != self)
		parent.ShowLinks (document.getElementById ("links").checked);
}

function ShowOld()
{
	if (parent != self)
		parent.ShowOld (document.getElementById ("old").checked);
}

function CheckSize ()
{
	var input = document.getElementById ("last");
	var value = parseInt (input.value);
	if (value == "NaN")
		input.value = last;
	else
	{
		if (value < 4)
			input.value = last = 4;
		else if (value > 24)
			input.value = last = 24;
		else
			input.value = last = value;

		if (parent != self)
			parent.ChangeLast (last);
	}
}

function Decrease ()
{
	var input = document.getElementById ("last");
	input.value = last = Math.max (4, parseInt (input.value) - 1);

	if (parent != self)
		parent.ChangeLast (last);
}

function Increase ()
{
	var input = document.getElementById ("last");
	input.value = last = Math.min (24, parseInt (input.value) + 1);

	if (parent != self)
		parent.ChangeLast (last);
}
