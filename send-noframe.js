var createRequest, sendRequest, position, from = 0, timeWait, generator = 0,zero=0;

function Send_GetKey (gen)
{
	var genString = gen.toString (16);
	while (genString.length != 8)
		genString = "0" + genString;

	var string = genString
		+ parent.recv.document.getElementById ("display").name
		+ document.getElementById ("hiddenHook")
		+ document.title
		+ parent.help.document.entity;
	return genString + DoWhateverNeedToBeDone (string);
}

function Send_SetStatus (text)
{
	document.getElementById ("status").innerHTML = text;
}

function Send_Init ()
{
	if (parent != self)
		parent.InitSend (self);
	else
	{
		var options = new Object ();
		options["name"] = "";
		options["wait"] = 60;
		options["urgent"] = true;
		Send_InitRemote (options);
	}
}

function Send_InitRemote (options)
{
	timeWait = 6000 * options["wait"];

	document.getElementById ("name").value = options["name"];

	if (window.XMLHttpRequest)
		createRequest = function () {return new XMLHttpRequest ();};
	else if (window.ActiveXObject)
		createRequest = function () {return new ActiveXObject ("Microsoft.XMLHTTP");};
	else
		createRequest = null;

	if (createRequest == null)
		Send_SetStatus ('Hm, anscheinend unterst�tzt dein Browser kein XMLHttpRequest-Object.');

	sendRequest = null;

	generator = options["generator"];
}

function Send_SetPosition (value)
{
	position = value;
}

function Send_StateChanged ()
{
	if (sendRequest.readyState == 4)
	{
		if (sendRequest.status >= 200 && sendRequest.status < 300)
		{
			Send_SetStatus ("");
			var
			message = document.getElementById ("message");
			message.value = "";
			message.focus ();
		}
		else
			Send_SetStatus ("Dein Post konnte nicht �bertragen werden (" + sendRequest.status + ", " + sendRequest.statusText + ").<br>" + sendRequest.responseText);

		sendRequest = null;
		++from;
	}
}

function Send_OnTimeout (to)
{
	if (from == to)
	{
		Send_SetStatus ("Der Server antwortete nicht innerhalb von " + (timeWait / 1000) + " Sekunden auf deine Postsendung.");

		sendRequest.abort ();
		sendRequest = null;
	}
}

function Send ()
{
	if (createRequest != null) {
		if (sendRequest == null)
			{
				Send_SetStatus ("Sende Post ...");
				sendRequest = createRequest;
				sendRequest.onreadystatechange = Send_StateChanged;
				sendRequest.open ("POST", "post.php", true);
				sendRequest.setRequestHeader ("Content-Type", "application/x-www-form-urlencoded");
				sendRequest.setRequestHeader ("Content-Encoding", "utf-8");
				//%%user \neq bot
				var content =
					"delay=" + position + "&name=" + encodeURIComponent (document.getElementById ("name").value) + "&message=" + encodeURIComponent (document.getElementById ("message").value)+"&bottag="+zero;
				if (generator)
					content += "&key=" + GetKey (generator++);
				sendRequest.send (content);
				setTimeout ("Send_OnTimeout (" + from + ")", timeWait);
			}
		else
			Send_SetStatus ("Dein alter Post wird noch gesendet ...");
	}
	else
	    alert("createrequest ist put");
}
