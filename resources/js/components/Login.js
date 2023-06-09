import React from "react";
import ReactDOM from "react-dom";

function Login() {
    return (
        <div className="container">
            <div className="row justify-content-center">
                <div className="col-md-8">
                    <div className="card">
                        <div className="card-header">Example Component</div>

                        <div className="card-body">I'm an Login component!</div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Login;

if (document.getElementById("login-page")) {
    ReactDOM.render(<Login />, document.getElementById("login-page"));
}
