// @link_with_parent

inx.ns("inx.code.lang").php = {
    normal:{
        triggers:[
            {re:/\$\w+/,name:"variable"},
            {re:/\d+/,name:"digit"},
            {re:/\/\*/,name:"comment_block"},
            {re:/"/,name:"string"},
            {re:/\/\/.*/,name:"comment"},
            {re:/(foreach|public|private|protected|static|final|extends|implements|function|array|return|echo|class|extends|if|else|foreach|while|do)(?=[^a-zA-Z0-9\_])/,name:"keyword"}
        ],
        style:"normal"
    },
    comment_block:{
        triggers:[
            {re:/\*\//,name:"back"}
        ],
        style:"comment"
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
    quote_escape:{},
    keyword:{style:"keyword"},
    comment:{style:"comment"}        
}