


function IconView(e){

if(!document.getElementsByTagName('DIV')['icon_win']){
	return false;
}

const ob_t=e.target;

const rect=ob_t.getBoundingClientRect();
const x=Math.floor(rect.left);
const w=Math.floor(rect.width);
const y=Math.floor(rect.top);
const h=Math.floor(rect.height);


const ob = document.getElementById("icon_win");

ob.style.top=y+h+5+'px';
ob.style.left=x-5+'px';

if(!ob.style.display || ob.style.display == "none"){
	ob.style.display="flex";
}
else if(ob.style.display == "flex"){
	ob.style.display="none";
}

}

function MakeIconWin(p){

let col=6;
const pagemax=20;
const imgdir='./img/';


if(typeof(document.forms['fo'].elements['icon']) === "undefined"){
	return false;
}
if(document.getElementsByTagName('DIV')['icon_win'] === "undefined"){
	return false;
}

const ob=document.getElementById("icon_win");


let tb="";
let w="";
let h="";
let txt="";
let img_array=new Array();
let idx_array=new Array();
for(let i=0;i < document.fo.icon.options.length;i++){
	let img=document.fo.icon.options[i].value;
	if(img){
		img_array.push(img);
		idx_array.push(i);
	}
}



if(!p){
	let pre= new Array(img_array.length);
	let i=0;
	for(img of img_array){
		pre[i]=new Image();
		pre[i].src=imgdir+img;
		i++;
	}
}


let pagetmp="";
const page=parseInt(p);
const start=page+1;
const end=page+pagemax;
const max=img_array.length;
const page_next=page+pagemax;
const page_back=page-pagemax;

if(max < col){
	col=max;
}


let u=0;
for(i=0;i<max;i++){
	u++;
	if(u > end){
		break;
	}
	if(u >= start){
		img=img_array[i];

		txt=document.fo.icon.options[idx_array[i]].textContent;

		tb+="<div class=\"icon_box\">";
		tb+="<div>";
		tb+="<div>";
		tb+="<a href=\"javascript:void(0)\" onclick=\"Select('"+idx_array[i]+"')\">";
		tb+="<img src=\""+imgdir+img+"\" border=\"0\">";
		tb+="</a>";
		tb+="</div>";
		tb+="</div>";
		tb+="<div>";
		tb+="<div>";
		tb+=txt;
		tb+="</div>";
		tb+="</div>";
		tb+="</div>";

	}
}



const cl="<div id=\"icon_win_btn_wrap\"><button type=\"button\" id=\"icon_win_close\"><span></span><span></span></button></div>";
let pg="<div id=\"icon_page\">";
if(page_back >= 0){
	pg+="<a href=\"javascript:void(0)\" onclick=\"MakeIconWin('"+page_back+"');\">&lt;&lt;前</a>";
}
if(page_next < max){
	pg+=" <a href=\"javascript:void(0)\" onclick=\"MakeIconWin('"+page_next+"');\">次&gt;&gt;</a>";
}

pg+="</div>";

tb=cl+tb+pg;

ob.innerHTML = tb;

document.getElementById("icon_win_close").addEventListener('click',IconBoxClose);
document.getElementById("icon_win").addEventListener('click',IconBoxClose);

}

function IconBoxClose(e){
	const tag=e.target.tagName;
	if(tag == "A"){
		return false;
	}
	document.getElementById('icon_win').style.display="none";
}


function Select(v){
	document.fo.icon.selectedIndex=parseInt(v);
	document.getElementById('icon_win').style.display="none";
}


function SetEnqEvent(){
	const els=document.querySelectorAll(".enquet_a");
	els.forEach(function(el) {
		el.addEventListener('pointerdown',function(){
			let no=el.dataset.no;
			let a=el.dataset.a;
			Enq(no,a);
		});
	});
}


function ReadData(n){
const storage=sessionStorage;
let data=storage.getItem(n);
if(data == null){
	data="";
}
return data;
}

function SetData(n,v){
const storage=sessionStorage;
storage.setItem(n,v);
}

function Enq(no,a){
const enq_data=ReadData(no);
let enq_ary;
if(enq_data){
	enq_ary=enq_data.split(":");
}
else{
	enq_ary=new Array();
}

let flag=false;
for(let n of enq_ary){
	if(n == no){
		flag=true;
		break;
	}
}
if(flag){
	alert("アンケートの連続投票はできません。");
	return false;
}

enq_ary.push(no);

SetData(no,enq_ary.join(":"));

let data={};
data['mode']="enquet";
data['no']=no;
data['a']=a;
Send(EncodeHTMLForm(data));
}

function Send(dt){
const xhr=new XMLHttpRequest();
xhr.open("POST","pbbs.php");
xhr.onreadystatechange = function(){
	switch(xhr.readyState){
		case 0:
			break;
		case 1:
			break;
		case 2:
			break;
		case 3:
			break;
		case 4:
			if(xhr.status == 200 || xhr.status == 304) {
				const txt=xhr.responseText;
				try{
					const data = JSON.parse(txt);
					if(data['result'] == "ok"){
						ShowCnt(data);
					}
					else{
						Err(data['err']);
					}
				}
				catch(e){
					const r=txt.indexOf("<body>");
					const r2=txt.indexOf("</body>");
					let a="";
					if(r != -1 && r2 != -1){
						let tmp=txt.split("<body>");
						tmp=tmp[1].split("</body>");
						a=tmp[0];
					}
					else if(e.message.indexOf("at") != -1){
						a=e.message;
					}
					else{
						a=txt;
					}
					console.log(e);
					Err(a);
				}
			}
			break;
	}
}

xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=UTF-8');
xhr.send(dt);
}

function ShowCnt(data){
const total=data['total'];
const ary=data['ary'];
const no=data['no'];

let top=0;
for(let d of ary){
	let c=parseInt(d['c']);
	if(top < c){
		top=c;
	}
}

for(let d of ary){
	let a=d['a'];
	let p=d['p'];
	let c=d['c'];
	

	
	let ob=document.getElementById("q_"+no+"_"+a);
	ob.style.width=p+'%';
	let ob_p=document.getElementById("p_"+no+"_"+a);
	ob_p.innerText=p+'%';
	
	ob.classList.remove("bar_top");
	ob.classList.remove("bar_lower");
	if(c && c == top){
		ob.classList.add("bar_top");
	}
	else{
		ob.classList.add("bar_lower");
	}
	
}

const ob_t=document.getElementById("t_"+no);
ob_t.innerText=total;

}

function Err(m){
document.getElementById("msg").innerHTML=m;
return false;
}


function EncodeHTMLForm(data){
let params=[];
for(let name in data){
	let value=data[name];
	let param=encodeURIComponent(name)+'='+encodeURIComponent(value);
	params.push(param);
}
return params.join('&').replace(/%20/g,'+');
}

function Css(){
const ua = window.navigator.userAgent;
if(ua.indexOf('Firefox') != -1) {
	const obs=document.querySelectorAll('.fuki_box');
	obs.forEach(function(ob) {
		ob.style.writingMode="horizontal-tb";
		ob.style.maxWidth="500px";
		ob.style.overflow="hidden";
	});
}

}

let iniflag=false;

function Ini(required){

for(let n in required){
	let elements=document.getElementsByName(n);
	let ob=elements.item(0);
	ob.classList.remove("input_alert");
	let ob_e=document.getElementById("e_"+n);
	ob_e.innerText="";
	ob_e.classList.remove("box_alert");
	ob_e.style.display="none";
}

}


function UnCheck(n,a){
	if(typeof(a[n]) === "undefined"){
		a[n]=[];
	}
}

function Check(){

let ids={};

let required={
'name':'お名前',
'email':'メール',
'comment':'コメント'
};

if(iniflag){
	Ini(required);
}

for(let n in required){
	let v=document.forms['fo'].elements[n].value;
	if(n != "email"){
		if(v == ""){
			UnCheck(n,ids);
			ids[n].push('※'+required[n]+'が入力されていません。');
		}
	}
	else{
		if(v){
			if(!v.match(/^[A-Za-z0-9]+[\w+-\.]+@[\w\.-]+\.\w{2,}$/)){
				UnCheck(n,ids);
				ids[n].push('※メールアドレスが正しくありません。');
			}
		}
	}
}

if(!iniflag){
	iniflag=true;
}

let flag=false;
for(let n in ids){
	flag=true;
	break;
}

if(flag){
	for(let n in ids){
		let elements=document.getElementsByName(n);
		let ob=elements.item(0);
		ob.classList.add("input_alert");
		let ob_e=document.getElementById("e_"+n);
		
		if(ob_e == null){
			ob_e=document.createElement('div');
			ob_e.setAttribute("id", "e_"+n);
			ob_e.setAttribute("class", "box_alert");
		}
		else{
			ob_e.setAttribute("class", "box_alert");
		}

		let pa = ob.closest("div");
		pa.appendChild(ob_e);

		let s="";
		for(let i=0;i<ids[n].length;i++){
			s+=ids[n][i]+"<br>";
		}
		ob_e.innerHTML=s;
		ob_e.style.display="block";
	}
	return false;
}

return true;
}


window.onload = function(){
SetEnqEvent();
MakeIconWin(0);
Css();
}
