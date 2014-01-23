// @link_with_parent

inx.ns("inx.code.lang").ini = {
    normal:{
        triggers:[
            {re:/\[/,name:"section"},        
            {re:/\$\w+/,name:"variable"},
            {re:/\d+/,name:"digit"},
            {re:/"/,name:"string"},
            {re:/\;.*/,name:"comment"}
        ],
        style:"normal"
    },
    variable:{style:"variable"},
    digit:{style:"digit"},
    string:{
        triggers:[
            {re:/"/,name:"back"},
            {re:/\\"/,name:"quote_escape"}
        ],
        style:"string"
    },
    section:{
        triggers:[
            {re:/\]/,name:"back"}
        ],
        style:"keyword"
    },    
    quote_escape:{},
    comment:{style:"comment"}        
}