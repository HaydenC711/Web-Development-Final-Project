function playSound() {
		const x = document.getElementById("car");
		x.setAttribute("controls", "controls");
		document.body.appendChild(x);
		}
		
function playSoundOther() {
		const x = document.getElementById("wrench");
		x.setAttribute("controls", "controls");
		document.body.appendChild(x);
		}
		
function muteMe() {
	const x = document.getElementById("lobby");
    x.muted = true;
    x.pause();
}
