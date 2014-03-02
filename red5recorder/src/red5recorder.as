// ActionScript file
import flash.external.ExternalInterface;
import flash.utils.Timer;

import mx.controls.Alert;
import mx.core.FlexGlobals;

private var camera:Camera;
private var mic:Microphone;
private var cam:Camera;
private var nc:NetConnection;
private var ns:NetStream;
private var timer:Timer;
private var video:Video;
private var fileName:String;
public  var streamer:String;
private var blinkSelected:Boolean;

/**
 * Connects to adobe media server/ red5
 *
 */
private function prepareStreams():void {
	ns= new NetStream(nc);
	
	mic =Microphone.getMicrophone();
	mic.setUseEchoSuppression(true);
	mic.setSilenceLevel(0);
	mic.gain = 75;
	
	cam = Camera.getCamera();
	cam.setMode(640, 480, 25);
	cam.setQuality(0,90);
	
	var timer:Timer=new Timer(50);
	timer.addEventListener(TimerEvent.TIMER, drawMicLevel);
	timer.start();
	
	ns.attachAudio(mic);
	ns.attachCamera(cam);
	this.video.attachCamera(cam);
}

private function init():void {
	if(FlexGlobals.topLevelApplication.parameters.fileName!=null) this.fileName = FlexGlobals.topLevelApplication.parameters.fileName;
	if(FlexGlobals.topLevelApplication.parameters.streamer!=null){
		this.streamer = FlexGlobals.topLevelApplication.parameters.streamer;
	}else{
		this.streamer = "rtmp://62.210.180.87/red5recorder/";
	}
	
	nc = new NetConnection();
	nc.addEventListener(NetStatusEvent.NET_STATUS,netStatusHandler);
	nc.connect(this.streamer);
	
	this.video = new Video();
	//this.video.opaqueBackground=true;
	this.videoContainer.video = video;	
	
	ExternalInterface.addCallback("startRecording", startRecording);
	ExternalInterface.addCallback("stopRecording", stopRecording);
	ExternalInterface.addCallback("playRecording", playRecording);
}

/**
 * Starts recording
 */
private function startRecording():void {    
	ns.publish(this.fileName,"record");
	blink1.displayed = true;
	//this.recordButton.enabled=false;
	//this.stopButton.enabled=true;
	
}

/**
 * Stop recording
 */
private function stopRecording():void {
	ns.close();
	blink1.displayed = false;
	//this.video.attachCamera(null);
	//this.recordButton.enabled=true;
	//this.stopButton.enabled=false;
	//this.playButton.enabled=true;
}



private function playRecording():void {
	this.ns.play(this.fileName);
	
	this.video.attachNetStream(ns);
	
}


/**
 * When the connection is successful we can
 * enable the record button.
 * 
 */
private function netStatusHandler(event:NetStatusEvent):void
{
	switch (event.info.code) {
		case "NetConnection.Connect.Failed":
			mx.controls.Alert.show("ERROR:Could not connect to : "+this.streamer);
			break;	
		case "NetConnection.Connect.Success":
			prepareStreams();
			break;
		default:
			ns.close();
		break;
	}
}

private function drawMicLevel(evt:TimerEvent):void {
	var ac:int=mic.activityLevel;
	micLevel.setProgress(ac,100);
}



private function onMicStatus(event:StatusEvent):void
{
	if (event.code == "Microphone.Unmuted")
	{
		mx.controls.Alert.show("Microphone access was allowed.");
	} 
	else if (event.code == "Microphone.Muted")
	{
		mx.controls.Alert.show("Microphone access was denied.");
	}
};

public function webcamParameters():void {
	Security.showSettings(SecurityPanel.DEFAULT);
}