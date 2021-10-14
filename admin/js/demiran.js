/**
 * DEMIRAN Frontend Core Object
 *
 * @author Attila Reterics
 * @license BSD-3-Clause
 * @copyright Attila Reterics
 * @date 13.10.2020
 * @contact reterics.attila@gmail.com
 */

const Misc = {
    /**
     * Generate Random Strings for unique IDs
     * @param num
     * @returns {string}
     */
    generateID(num){
        if(!num || typeof num !== "number"){
            num = 5;
        }
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (let i = 0; i < num; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }

        return text;
    },
    generateIDByUnix(){
        const timestamp = new Date().getTime();
        const timestampString = timestamp.toString();
        const timestampLength = timestampString.length;
        const characters = "abcdefghijklmnopqrstuvwxyz";

        let id = "";
        for(let i = 0; i<timestampLength; i++){
            const char = parseInt(timestampString[i]);
            if(!Number.isNaN(char) && characters[char]){
                id+=characters[char];
            }
        }
        return id;

    }
};

const Demiran = {
    /**
     *
     * @param {string} imageSelector
     * @param {string} areaSelector
     * @param {string} fileSelector
     */
    activateFileDrop: (imageSelector, areaSelector, fileSelector) => {
        const image = document.querySelector(imageSelector);
        const area = document.querySelector(areaSelector);
        const fileInput = document.querySelector(fileSelector);
        if (area) {
            area.addEventListener('dragover', (event) => {
                event.stopPropagation();
                event.preventDefault();
                // Style the drag-and-drop as a "copy file" operation.
                event.dataTransfer.dropEffect = 'copy';
                area.style.backgroundColor = "#eeeeee";
            });

            area.addEventListener('drop', (event) => {
                event.stopPropagation();
                event.preventDefault();
                const fileList = event.dataTransfer.files;
                handleFiles(fileList,image,area);
                area.style.backgroundColor = null;
            });


            if (window.FileList && window.File && window.FileReader) {
                fileInput.onchange = function(event){
                    handleFiles(event.target.files,image,area);
                };
            }
            if(fileInput){
                area.onclick = function () {
                    fileInput.click();
                }
            }

        }
    },
    /**
     *
     * @param {function} callback
     */
    ready: (callback) => {
        if (document.readyState !== "loading") callback();
        else document.addEventListener("DOMContentLoaded", callback);
    },
    /**
     * @param {object} json
     * @returns {string}
     */
    convertToFormEncoded: (json)=>{
        if(typeof json === "string"){
            return json;
        }
        if(typeof json === "number"){
            return json.toString();
        }
        if (json instanceof HTMLFormElement && json.tagName && json.tagName.toLowerCase() === "form") {
            json = new FormData(json);
        }
        if (json instanceof FormData){
            const formData = json;
            json = {};
            for (const [key, value] of formData.entries()) {
                console.log(key, value);
                if(key.endsWith("[]")){
                    if(Array.isArray(json[key])){
                        json[key].push(value);
                    } else {
                        json[key] = [value];
                    }
                } else {
                    json[key] = value;
                }

            }
        }
        if(!json || typeof json != "object") {
            return "";
        }
        let uri = "";
        const keys = Object.keys(json);
        keys.forEach(key=>{
            const value = json[key];
            switch (typeof value) {
                case "string":
                    uri+="&"+key+"="+encodeURIComponent(value);
                    break;
                case "number":
                    uri+="&"+key+"="+encodeURIComponent(value);
                    break;
                case "object":
                    if(!value){
                        uri+="&"+key+"=";
                    } else if(Array.isArray(value)){
                        let text = "";
                        value.forEach(v=>{
                            text+="&"+key+"="+v;
                        });
                        uri+=text;
                    }
                    break;
            }
        });
        return uri.substring(1);
    },
    /**
     * @param {HTMLFormElement} htmlFormElement
     * @returns {FormData}
     */
    getMultiPartForm: (htmlFormElement)=>{
        //multipart/form-data
        return new FormData(htmlFormElement);
    },
    /**
     * @returns {HTMLElement}
     */
    createLoading: ()=>{
        let loading = document.getElementById("loading");
        if(!loading) {
            loading = document.createElement("div");
            loading.id = "loading";
            const loadingImage = document.createElement("div");
            loadingImage.className = "loadingImage";
            loading.appendChild(loadingImage);
            document.body.appendChild(loading);
        } else {
            loading.style.display = null;
        }
        return loading;
    },
    /**
     *
     * @param {string} url
     * @param {object|FormData|HTMLFormElement|string} body
     * @param {function} callback
     * @returns {boolean}
     */
    post: (url, body, callback = ()=>{}) => {
        const xHTTP = new XMLHttpRequest();
        const loadingNode = Demiran.createLoading();
        xHTTP.onreadystatechange = function () {
            // code
            if (this.readyState === 4 && this.status === 200) {
                if(loadingNode){
                    loadingNode.style.display = "none";
                }
                callback(false, xHTTP.response, this.status);
            } else if (this.readyState === 4) {
                if(loadingNode){
                    loadingNode.style.display = "none";
                }
                callback(true, xHTTP.response, this.status);
            }
        };
        xHTTP.open('POST', url);

        if (body instanceof FormData) {
            //xHTTP.setRequestHeader('Content-Type', 'application/multipart/form-data')
        } else if (typeof body === "string") {
            xHTTP.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        } else if (typeof body === "object" && body) {
            xHTTP.setRequestHeader('Content-Type', 'application/json');
            body = JSON.stringify(body);
        } else if (body instanceof HTMLFormElement) {
            body = new FormData(body);
        } else {
            if(loadingNode){
                loadingNode.style.display = "none";
            }
            callback("Invalid input body");
            return false;
        }
        xHTTP.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        try {
            const requestBody = typeof body === "string" || body instanceof FormData ? body : body.toString();
            xHTTP.send(requestBody);
        } catch (e) {
            if(loadingNode){
                loadingNode.style.display = "none";
            }
            callback(e.message);
        }
    },
    /**
     * @param {string} url
     * @param {function} callback
     */
    get: (url, callback = ()=>{}) => {
        const xHTTP = new XMLHttpRequest();
        const loadingNode = Demiran.createLoading();

        xHTTP.open('GET', url, true);

        xHTTP.onload = function () {
            if(loadingNode){
                loadingNode.style.display = "none";
            }
            callback(null, xHTTP.response)
        };
        try {
            xHTTP.send(null);
        } catch (e) {
            if(loadingNode){
                loadingNode.style.display = "none";
            }
            callback(e.message);
        }
        xHTTP.send(null);
    },
    /**
     * This function connects the Frontend directly to Backend
     * @param {string} task_name
     * @param body
     * @param callback
     * @returns {boolean}
     */
    call: (task_name, body, callback = ()=>{})=>{
        body = Demiran.convertToFormEncoded(body);
        if(body.includes("&_call=")) {
            body = body.replace("&_call=","&__call=")
        } else if(body.startsWith("_call=")){
            body = "_"+body;
        }
        body = "_call="+encodeURIComponent(task_name)+"&"+body;
        return Demiran.post("process.php", body, callback);
    },
    /**
     * @param {string} parentSelector
     * @param {string} childSelector
     */
    applyDragNDrop: (parentSelector, childSelector) => {
        if(!parentSelector || !childSelector || typeof parentSelector !== "string" || typeof childSelector !== "string"){
            console.error("Invalid Input Selectors");
            return;
        }
        const parent = document.querySelector(parentSelector);
        if(!parent){
            console.error("Parent has not found.");
            return;
        }

        const children = parent.querySelectorAll(childSelector);
        if(!children || !children.length) {
            console.error("There is no child.");
            return;
        }

        const internalEvents = {
            _dragSource:null,
            dragStart: function (e) {
                this.style.opacity = '0.5';
                internalEvents._dragSource = this;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('order', this.style.order);
            },
            dragEnd: function () {
                this.style.opacity = '1';
                children.forEach(item => item.classList.remove('over'));
            },
            dragOver: function (e) {
                if(e && typeof e.preventDefault === "function"){
                    e.preventDefault();
                }
                return false;
            },
            dragEnter: function () {
                this.classList.add("over")
            },
            dragLeave: function () {
                this.classList.remove("over");
            },
            drop: function (e) {
                if(e && typeof e.stopPropagation === "function"){
                    // stops the browser from redirecting
                    e.stopPropagation();
                }

                if(internalEvents._dragSource && internalEvents._dragSource !== this){
                    internalEvents._dragSource.style.order = this.style.order;
                    this.style.order = e.dataTransfer.getData('order');
                }
            }
        };

        const handledOrders = [];
        children.forEach((node, index) => {
            if(node){
                const orderData = node.getAttribute("data-order");
                if(orderData){
                    if(handledOrders.includes(orderData)) {
                        const max =  Math.max.apply(null, handledOrders)+1;
                        handledOrders.push(max);
                        node.style.order = max.toString();
                    } else {
                        handledOrders.push(orderData);
                        node.style.order = orderData;
                    }

                }else {
                    node.style.order = index + 1;
                }

                node.style.touchAction = "none";
                node.style.display= "flex";
                node.style.opacity= "1";

                node.setAttribute("draggable","true");
                node.classList.add("dragged");
                node.ondragstart = internalEvents.dragStart;
                node.ondragend = internalEvents.dragEnd;
                node.ondragenter = internalEvents.dragEnter;
                node.ondragleave = internalEvents.dragLeave;
                node.ondrop = internalEvents.drop;
                node.ondragover = internalEvents.dragOver;
            }
        });

        /**
         * Apply Flexbox CSS for Drag-N-Drop
         */

        parent.style.display = "flex";
        parent.style.flexDirection = "column";
        parent.style.flexWrap = "nowrap";

    },
    /**
     * @param {string} str
     * @returns {string}
     */
    getStringColor: (str)=>{
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        const c = (hash & 0x00FFFFFF)
            .toString(16)
            .toUpperCase();


        const hex1 = ("00000".substring(0, 6 - c.length) + c).toLowerCase();
        const hex2 = "ffffff";
        //console.log(hex1,hex2);

        //for each color pair
        let hexC11 = parseInt(hex1.slice(0,2), 16);
        let hexC12 = parseInt(hex1.slice(2,4), 16);
        let hexC13 = parseInt(hex1.slice(4,6), 16);
        let hexC21 = parseInt(hex2.slice(0,2), 16);
        let hexC22 = parseInt(hex2.slice(2,4), 16);
        let hexC23 = parseInt(hex2.slice(4,6), 16);

        //calculate mean for each color pair
        let colorMean1 = (hexC11 + hexC21) / 2;
        let colorMean2 = (hexC12 + hexC22) / 2;
        let colorMean3 = (hexC13 + hexC23) / 2;

        //convert back to hex
        let colorMean1Hex = Math.floor(colorMean1).toString(16);
        let colorMean2Hex = Math.floor(colorMean2).toString(16);
        let colorMean3Hex = Math.floor(colorMean3).toString(16);

        //pad hex if needed
        if (colorMean1Hex.length === 1)
            colorMean1Hex = "0" + colorMean1Hex;
        if (colorMean2Hex.length === 1)
            colorMean2Hex = "0" + colorMean2Hex;
        if (colorMean3Hex.length === 1)
            colorMean3Hex = "0" + colorMean3Hex;

        //merge color pairs back into one hex color
        let avgColor = colorMean1Hex +
            colorMean2Hex +
            colorMean3Hex;

        return "#" + avgColor;


    },
    /**
     * @param {string} title
     * @param {string|HTMLElement|function|number} body
     * @param {object[]} buttons
     * @returns {HTMLElement}
     */
    generateLioModal: (title = "", body = "", buttons = [])=>{
        const main = document.createElement("div");
        main.classList.add("lio-modal");

        const modalId = "lio"+(new Date().getTime() + Math.floor((Math.random() * 1000) + 1));
        main.id = modalId;
        /**
         * Creating Header
         */

        const header = document.createElement("div");
        header.classList.add("header");
        const h5 = document.createElement("h5");
        h5.classList.add("title");
        h5.innerHTML = title;

        header.appendChild(h5);


        const rightSide = document.createElement("div");
        rightSide.classList.add("right");

        const close = document.createElement("span");
        close.classList.add("close-icon");
        close.onclick = ()=>{
            const modal = document.querySelector("#"+modalId);
            modal.outerHTML = "";
        };


        rightSide.appendChild(close);

        header.appendChild(rightSide);
        main.appendChild(header);

        /**
         * Creating Body
         */

        const content = document.createElement("div");
        content.classList.add("body");
        content.style.height = "auto";
        if(typeof body === "function"){
            body = body();
        }
        if(body instanceof HTMLElement){
            content.appendChild(body);
        } else if(typeof body === "string" || typeof body === "number" || typeof body === "boolean"){
            content.innerHTML = body;
            content.style.padding = "5px 10px";
        }



        main.appendChild(content);
        /**
         * Creating Footer Buttons
         */

        const footer = document.createElement("div");
        footer.classList.add("footer");
        footer.classList.add("mr-2");
        footer.classList.add("btn-group");


        if(Array.isArray(buttons)){
            buttons.forEach((button)=>{
                if(button && typeof button === "object"){
                    const element = document.createElement("button");
                    const text = button.value || button.text || button.innerHTML;
                    const classes = button.className || "";

                    /**
                     * Add every possible event to the button
                     */
                    const keys = Object.keys(button);
                    keys.forEach(key=>{
                        if(key.startsWith("on") && typeof button[key] === "function"){
                            element[key] = (e) => {
                                const closeDialog = ()=>{
                                    const modal = document.querySelector("#"+modalId);
                                    modal.outerHTML = "";
                                };
                                if(e && e.preventDefault){
                                    e.preventDefault();
                                }

                                button[key](closeDialog, modalId, e);
                            };
                        }
                    });
                    if(button.type === "close"){
                        element.onclick = ()=>{
                            const modal = document.querySelector("#"+modalId);
                            modal.outerHTML = "";
                        };
                    }

                    if(classes){
                        element.className = classes;
                    }
                    element.innerHTML = text;
                    element.classList.add("btn");
                    element.classList.add("btn-outline-black");
                    footer.appendChild(element);
                }
            });
        }
        main.appendChild(footer);

        return main;

    },
    /**
     *
     * @param {string} title
     * @param {string|HTMLElement|function|number} body
     * @param {object[]} buttons
     * @returns {{node: (HTMLElement), id: string, close: function}}
     */
    openPopUp: function(title, body, buttons){
        const modal = this.generateLioModal(title,body,buttons);
        const modalId = modal.id;
        modal.id = "";

        const background = document.createElement("div");
        background.style.position = "absolute";
        background.style.height = "100vh";
        background.style.width = document.body.offsetWidth + "px";
        background.style.top = "0";
        background.style.left = "0";
        background.style.overflowY = "hidden";
        background.style.overflowX = "hidden";
        background.style.backgroundColor = "rgba(255,255,255,0.4)";
        background.id = modalId;


        modal.style.width = "500px";

        modal.style.height = "auto";

        const header = modal.querySelector(".header");
        header.style.cursor = "pointer";


        let moveStarted = false;
        let startPosition = null;
        let xDif;
        let yDif;


        header.onmousedown = (event)=>{
            if(!moveStarted){
                const currentPosition = {
                    x: event.clientX,
                    y: event.clientY,
                };
                const rect = header.getBoundingClientRect();
                xDif = currentPosition.x - rect.left;
                yDif = currentPosition.y - rect.top;
                startPosition = {
                    x: event.clientX,
                    y: event.clientY,
                };
                if(xDif > 3 && yDif > 3) {
                    moveStarted = true;
                }

            }
        };

        background.onmousemove=(event)=>{
            if(moveStarted){
                const currentPosition = {
                    x: event.clientX,
                    y: event.clientY,
                };
                modal.style.left = (currentPosition.x-xDif) + "px";
                modal.style.top = (currentPosition.y-yDif) + "px";
            }

        };
        background.onmouseup = ()=>{
            if(moveStarted) {
                moveStarted = false;
            }
        };


        background.appendChild(modal);
        document.body.appendChild(background);
        const rect = modal.getBoundingClientRect();
        modal.style.left = ((window.innerWidth - rect.width)/2) + "px";
        modal.style.top = ((window.innerHeight - rect.height)/4) + "px";
        return {
            node: modal,
            close: ()=>{
                const modal = document.querySelector("#"+modalId);
                if(modal){
                    modal.outerHTML = "";
                }
            },
            id: modalId
        };
    },
    /**
     *
     * @param {string} title
     * @param {string|HTMLElement|function|number} body
     * @param {function} callback
     * @returns {*|{node: HTMLElement, id: string, close: Function}}
     */
    confirm: function (title, body, callback=()=>{}) {

        const buttons = [
            {
                value: "Igen",
                onclick: (closeDialog)=>{
                    closeDialog();
                    callback(true);
                }

            },
            {
                value: "Vissza",
                onclick: (closeDialog)=>{
                    closeDialog();
                    callback(false);
                }
            }
        ];
        return this.openPopUp(title,body,buttons);

    },
    /**
     * Replaces the original alert function
     * @param {string} text
     * @param title
     */
    alert: function (text, title) {
        const alertBox =  this.openPopUp(title || "Üzenet", text, [{
            value:"OK",
            type:"close"
        }]);
        if(alertBox){
            document.onkeyup = function (e){
                if(e.key === "Enter" || e.which === 13 || e.keyCode === 13) {
                    document.onkeyup = null;
                    alertBox.close();
                }
            }
        }
    },
    /**
     *
     * @param {string} imageSelectors
     */
    imageViewer: function (imageSelectors){
        const nodes = document.querySelectorAll(imageSelectors);
        const self = this;
        nodes.forEach(function (node) {
            const src = node.getAttribute("src");
            if(node.tagName.toLowerCase() === "img" && src){
                node.onclick = function () {
                    const image = document.createElement("img");
                    image.setAttribute("src", src);
                    image.style.width = "100%";
                    image.onload = function () {
                        const width = Math.min(image.naturalWidth, window.innerWidth * 0.8);

                        //image.style.maxWidth = width + "px";
                        console.log();

                        const popup = self.openPopUp("Kép", image, {
                            value:"Bezárás",
                            type:"close"
                        });
                        const popupWidth = (width / window.innerWidth*100);
                        const popupLeft = (100-popupWidth) / 2;

                        image.style.maxWidth = width + "px";
                        popup.node.style.maxWidth = width + "px";
                        popup.node.style.width = (width / window.innerWidth*100)+"%";
                        popup.node.style.left = popupLeft+"%";
                    };
                };

            }
        });
    },
    /**
     * This function initiates a file download
     * @param {string} file_name
     * @param {string} string
     * @param file_type
     */
    downloadData: function (file_name, string, file_type = '') {
        if(!file_type){
            file_type =  'text/plain';
        }
        if(!file_name) {
            file_name = Math.floor(new Date().getTime()/360000) + ".txt";
        }
        try {
            let textToSaveAsBlob = new Blob([string], {type: file_type});
            let textToSaveAsURL = window.URL.createObjectURL(textToSaveAsBlob);
            let fileNameToSaveAs = file_name;

            let downloadLink = document.createElement('a');
            downloadLink.download = fileNameToSaveAs;
            downloadLink.innerHTML = 'Download As File';
            downloadLink.href = textToSaveAsURL;
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);

            downloadLink.click();
            downloadLink.outerHTML = '';


        }catch (e) {
            console.error(e.message);
        }
    },
    /**
     * @param task_name
     * @param uri
     * @returns {Promise<any>}
     */
    promisifyCall: function (task_name, uri){
        const self = this;
        return new Promise((resolve, reject) => {
            self.call(task_name, uri, function(error,result){
                if(error) {
                    reject(result);
                } else {
                    let json = null;
                    try {
                        json = JSON.parse(result);
                    }catch (e) {

                    }
                    if(json){
                        resolve(json);
                    } else {
                        resolve(result);
                    }
                }
            })
        })
    },
    /**
     * Initiate Functional Self Test
     */
    selfTest: async function (){

        const results = [];
        const users = await this.promisifyCall("get_user_list");
        if(Array.isArray(users)) {
            console.log("User List: OK");
            results.push(true);
        } else {
            console.log("User List: HIBA");
            results.push(false);
            return;
        }

        await this.promisifyCall("add_user", Demiran.convertToFormEncoded({
            username:"_____testuser",
            email:"_____testuser@test.com",
            password:"1234",
            password_confirmation:"1234"
        }));
        let users_after = await this.promisifyCall("get_user_list");
        let lastUser = Array.isArray(users_after) ? users_after.pop() : null;
        if(lastUser && lastUser.username === "_____testuser") {
            console.log("Add User: OK");
            results.push(true);
        } else {
            console.log("Add User: HIBA");
            results.push(false);
            return;
        }

        await this.promisifyCall("delete_user", Demiran.convertToFormEncoded({
            deleteuser:lastUser.id
        }));
        users_after = await this.promisifyCall("get_user_list");
        lastUser = Array.isArray(users_after) ? users_after.pop() : null;
        if(lastUser && lastUser.username !== "_____testuser") {
            console.log("Delete User: OK");
            results.push(true);
        } else {
            console.log("Delete User: HIBA");
            results.push(false);
            return;
        }

        const projects = await this.promisifyCall("get_project_list");
        if(Array.isArray(projects)) {
            console.log("Project List: OK");
            results.push(true);
        } else {
            console.log("Project List: HIBA");
            console.log(projects);
            results.push(false);
            return;
        }

        await this.promisifyCall("add_user", Demiran.convertToFormEncoded({
            username:"_____testuser",
            email:"_____testuser@test.com",
            password:"1234",
            password_confirmation:"1234",
            role:"member"
        }));

        await this.promisifyCall("add_user", Demiran.convertToFormEncoded({
            username:"_____testclient",
            email:"_____testclient@test.com",
            password:"1234",
            password_confirmation:"1234",
            role:"client"
        }));
        users_after = await this.promisifyCall("get_user_list");

        const client = users_after.pop();
        const member = users_after.pop();


        await this.promisifyCall("add_project", Demiran.convertToFormEncoded({
            users:member.id,
            title:"TestProject",
            category:"PHP-MySQL",
            client:client.id,
            billing:"fixed"
        }));

        let projects_after = await this.promisifyCall("get_project_list");
        let lastProject = projects_after.pop();
        if(lastProject && lastProject.title === "TestProject") {
            console.log("Add Project: OK");
            results.push(true);
        } else {
            console.log("Add Project: HIBA");
            results.push(false);
            return;
        }
        const projects_flow = await this.promisifyCall("get_project_flow");
        if(projects_flow && Array.isArray(projects_flow.nodes)) {
            console.log("Project Graph: OK");
            results.push(true);
        } else {
            console.log("Project Graph: HIBA");
            results.push(false);
            return;
        }

        await this.promisifyCall("delete_project", Demiran.convertToFormEncoded({
            deleteproject:lastProject.id
        }));

        projects_after = await this.promisifyCall("get_project_list");
        lastProject = projects_after.pop();
        if(lastProject && lastProject.title !== "TestProject") {
            console.log("Delete Project: OK");
            results.push(true);
        } else {
            console.log("Delete Project: HIBA");
            results.push(false);
            return;
        }

        //Delete Test Users
        await this.promisifyCall("delete_user", Demiran.convertToFormEncoded({
            deleteuser:member.id
        }));
        await this.promisifyCall("delete_user", Demiran.convertToFormEncoded({
            deleteuser:client.id
        }));
    },
    resizeFunctions:[],
    addResize(method){
        if(typeof method === "function") {
            this.resizeFunctions.push(method);
        }
    },
    removeResize(method) {
        if(typeof method === "function") {
            const tempArray = [];
            this.resizeFunctions.forEach(function(m){
                if(m !== method) {
                    tempArray.push(m);
                }
            });
            if(tempArray.length !== this.resizeFunctions.length) {
                this.resizeFunctions = tempArray;
            }
        }
    },
    applyResize:function(node){
        if(!node) {
            node = window;
        }
        if(node) {
            const self = this;
            let timeout;
            node.onresize = function(){
                clearTimeout(timeout);

                timeout = setTimeout(function(){
                    for(let i = 0; i < self.resizeFunctions.length; i++) {
                        if(typeof self.resizeFunctions[i] === "function") {
                            self.resizeFunctions[i]();
                        }
                    }
                }, 400);
            }
        }
    }
};



function getDistanceFromLatLonInM(lat1,lon1,lat2,lon2) {
    function deg2rad(deg) {
        return deg * (Math.PI/180)
    }
    const R = 6371;
    const dLat = deg2rad(lat2-lat1);
    const dLon = deg2rad(lon2-lon1);
    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon/2) * Math.sin(dLon/2)
    ;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const d = R * c;
    return d * 1000;
}

const GET = {};

function loadGETParameters() {
    const params = window.location.search.substr(1);
    params.split("&")
        .forEach(function (item) {
            const tmp = item.split("=");
            GET[tmp[0]] = tmp[1];
        });
}
loadGETParameters();

const removeTask = function (id, title) {
    Demiran.openPopUp("Jóváhagyás", "Biztonsan törölni szeretnéd ezt a Feladatot? <br> " + id + " - " + title, [
        {
            value:"Igen",
            onclick: (closeDialog)=>{
                closeDialog();
                Demiran.call("delete_project_task",Demiran.convertToFormEncoded(form),function(error,result){
                    if(!error && result.trim() === "OK"){
                        location.reload();
                    } else {
                        Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                        console.log(result,error);
                    }
                });
            }
        },
        {
            value:"Vissza",
            type:"close"
        }
    ]);

    return false;
};

const editTask = function (id, viewOnly = true) {

    const editTaskDivOuter = document.getElementById("editTaskDivOuter");
    if(editTaskDivOuter){
        Demiran.call("get_task_details", 'get_task_details=' + id, function (e, result) {
            let json = null;
            try {
                json = JSON.parse(result);
            }catch (e) {
                console.error(e);
                console.error(result);
            }
            if (json) {
                const editTaskDivOuter = document.querySelector("#editTaskDivOuter form");
                if (editTaskDivOuter) {
                    const cln = editTaskDivOuter.cloneNode(true);
                    const idNode = document.createElement("input");
                    idNode.setAttribute("type", "hidden");
                    idNode.setAttribute("name", "id");
                    idNode.value = id;
                    cln.appendChild(idNode);

                    const title = cln.querySelector("[name=title]");
                    const project_id = cln.querySelector("[name=project_id]");
                    const users = cln.querySelector("#task-users");
                    const repeat = cln.querySelector("[name=repeat]");
                    const priority = cln.querySelector("[name=priority]");
                    const state = cln.querySelector("[name=state]");
                    const start_time = cln.querySelector("[name=start_time]");
                    const deadline = cln.querySelector("[name=deadline]");

                    if(title && project_id && users && repeat && priority && state && start_time && deadline){


                        title.value = json.title;
                        project_id.value = json.project;
                        repeat.value = json.repeat;
                        priority.value = json.priority;
                        state.value = json.state;
                        start_time.value = json.start_time.split(" ")[0];
                        deadline.value = json.deadline.split(" ")[0];

                        if(viewOnly){
                            title.disabled = true;
                            project_id.disabled = true;
                            repeat.disabled = true;
                            priority.disabled = true;
                            state.disabled = true;
                            start_time.disabled = true;
                            deadline.disabled = true;
                        }


                        const userNames = [];
                        const options = users.querySelectorAll("option");
                        (json.users || "").split(",").forEach(function(user){
                            options.forEach(function (option){
                                console.log(option.getAttribute("value"), user);
                                if(option.getAttribute("value") === user && user) {
                                    option.setAttribute("selected", "true");
                                    userNames.push(option.innerHTML);
                                }
                            })
                        });
                        if(viewOnly){
                            users.outerHTML = "<div>"+userNames.join(", ")+"</div>";
                        }

                    } else {
                        console.log("HTML Elemek hiányoznak az ablakból.");
                    }

                    if(viewOnly) {
                        const popup = Demiran.openPopUp("Feladat részletei", cln, [
                            {
                                value:"Bezárás",
                                type:"close"
                            }
                        ]);
                    } else {
                        const popup = Demiran.openPopUp("Feladat részletei", cln, [
                            {
                                value:"Mentés",
                                onclick: (closeDialog, modalID)=>{
                                    const modal = document.querySelector("#"+modalID);
                                    if(modal){
                                        const form = modal.querySelector("form");
                                        if(form){
                                            closeDialog();
                                            Demiran.call("update_project_task",Demiran.convertToFormEncoded(form),function(error,result){
                                                if(!error && result.trim() === "OK"){
                                                    Demiran.alert("Adatok mentése sikeres!");
                                                    location.reload();
                                                } else {
                                                    Demiran.alert("Hiba merült fel! Kérlek ellenőrizd a konzolt...", "Hiba");
                                                    console.log(result,error);
                                                }
                                            });
                                        }
                                    } else {
                                        Demiran.alert("Kritikus hiba a DOM-ban!");
                                    }

                                }
                            },
                            {
                                value:"Vissza",
                                type:"close"
                            }
                        ]);
                    }

                }
            } else {
                Demiran.alert("Hibás adat érkezett a szervertől.");
            }
        });
    } else {
        alert('Az alábbi oldal nem támogatja ezt a műveletet!');
    }
    //Demiran.alert('Ez a funckió nincs implementálva az MVPben');
};

window.applyTheme = function(themeName){
    let uri = "./admin/css/";
    if(location.href.includes("/admin")) {
        uri = uri.replace("/admin", "");
    }
    const availableStyle = document.getElementById("loadedTheme");
    if(!availableStyle && themeName !== "themeName"){
        const style = document.createElement("link");
        style.setAttribute("rel", "stylesheet");
        style.setAttribute("id", "loadedTheme");
        style.setAttribute("href", uri+"/theme-"+themeName+".css");

        document.head.appendChild(style);
    } else if(themeName !== "default"){
        availableStyle.setAttribute("href", uri+"./theme-"+themeName+".css");
    } else {
        availableStyle.setAttribute("href", "#");
    }

};

window.selectedTheme = window.localStorage.getItem("theme");
if(window.selectedTheme && window.selectedTheme !== "default"){
    window.applyTheme(window.selectedTheme);
}
Demiran.applyResize(window);