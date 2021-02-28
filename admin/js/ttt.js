


//https://www.staging.movingcompanies.com/form-thank-you-page/
let loadMSN = true;
const msn = "(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:\"27007670\"};\n" +
    "o.q=w[u],w[u]=new UET(o),w[u].push(\"pageLoad\")},n=d.createElement(t),n.src=r,n.async=1,n.onload=\n" +
    "n.onreadystatechange=function(){var s=this.readyState;s&&s!==\"loaded\"&&s!==\"complete\"||(f(),n.onload=\n" +
    "n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})\n" +
    "(window,document,\"script\",\"//bat.bing.com/bat.js\",\"uetq\");";
const msnNode = document.createElement("script");
msnNode.id = "msnCampaign";
msnNode.type = 'text/javascript';
msnNode.innerHTML = msn;

if(location.pathname.startsWith("/form-thank-you-page") && location.search && location.search.length > 3){
    console.log("This is the thank you page!");
    const adWords =   "gtag('event', 'conversion', {'send_to': 'AW-962040569/6iQUCOqr9mAQ-aXeygM'});\n";
    const adWordsNode = document.createElement('script');
    adWordsNode.type = 'text/javascript';
    adWordsNode.id = 'adWordCampaign';
    adWordsNode.innerHTML = adWords;



    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    const se = urlParams.get('se');
    if(se){
        let node;
        switch(se){
            case 'movecogoo':
                node = document.getElementById('adWordCampaign');
                if(!node){
                    document.body.appendChild(adWordsNode);
                    console.log("AdWords loaded for id: adWordCampaign");
                }
                break;
            case 'movecomsn2':
                loadMSN = false;
                console.log("MSN cannot be loaded in this page");
                break;
        }
    }
}
if(loadMSN){
    const nodeMSN = document.getElementById('msnCampaign');
    if(!nodeMSN){
        document.body.appendChild(msnNode);
        console.log("MSN loaded for id: msnCampaign");
    }
}

if(location.pathname.startsWith("/v4-form") && location.search && location.search.length > 3){
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const se = urlParams.get('se');
    const k = urlParams.get('k');
    if(se || k){
        document.querySelectorAll(".elementor-heading-title a").forEach(a=> {
            const href = a.getAttribute("href");
            if(href.endsWith("/v4")){
                a.setAttribute("href", href + queryString);
            } else {console.log(href)}

        });

    }
}




jQuery(document).ready(function(){
    setTimeout(function(){

        document.querySelectorAll("#site-header ul.slimmenu li a").forEach(function(a){

            if(a){
                const href = a.getAttribute("href").replace("auto-transport/","").replace("international-moving-companies/","").replace("auto-transport","").replace("international-moving-companies","");
                if(href && href.includes("getquotes.moving")){
                    let text = href.includes("www.") ? "moving" : "www.moving";
                    a.setAttribute("href",       href.replace("getquotes.moving", text));
                } else if(href && href.startsWith("/")) {
                    a.setAttribute("href","https://www.movingcompanies.com"+href)
                } else if(href && href.startsWith("https://movingcompanies.com")){
                    a.setAttribute("href",href.replace("https://movingcompanies.com","https://www.movingcompanies.com"))
                }
            }
        })
    },1000);

});

