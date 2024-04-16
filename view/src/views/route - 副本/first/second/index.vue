<template>
    <div class="container">
        <div class="nested">
            <div class="content">
                <span>嵌套路由1-1</span>
                <p>
                    <router-view />
                </p>
            </div>
        </div>
    </div>
</template>
<style scoped lang="scss">
    .container{
        background: #ffffff;
        padding: 20px;
        .nested{
            color: #41b882;
            background: #ecf8f3;
            width: 100%;
            padding: 8px 16px;
            margin: 0;
            box-sizing: border-box;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            opacity: 1;
            display: flex;
            align-items: center;
            .content{
                display: table-cell;
                padding: 0 8px;
                span{
                    font-weight: 700;
                    font-size: 13px;
                    line-height: 18px;
                }
                p{
                    font-size: 12px;
                    margin: 0;
                }
            }
        }
    }
</style>
